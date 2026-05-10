<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LogsController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()?->is_root) {
            abort(403, 'غير مصرح');
        }

        $type = $request->get('type', 'audit');
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        $auditLogs = collect();
        $loginLogs = collect();
        $timelineItems = collect();

        if ($type === 'audit') {
            $auditLogs = AuditLog::with('user')
                ->when($request->filled('search'), function ($q) use ($request) {
                    $search = trim($request->search);

                    $q->where(function ($sub) use ($search) {
                        $sub->where('action', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('target_type', 'like', "%{$search}%");
                    });
                })
                ->when($request->filled('action'), function ($q) use ($request) {
                    $q->where('action', $request->action);
                })
                ->when($request->filled('user_id'), function ($q) use ($request) {
                    $q->where('user_id', $request->user_id);
                })
                ->when($request->filled('from'), function ($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->from);
                })
                ->when($request->filled('to'), function ($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->to);
                })
                ->latest()
                ->paginate(20)
                ->withQueryString();
        }

        if ($type === 'login') {
            $loginLogs = LoginLog::with('user')
                ->when($request->filled('user_id'), function ($q) use ($request) {
                    $q->where('user_id', $request->user_id);
                })
                ->when($request->filled('ended_by'), function ($q) use ($request) {
                    $q->where('ended_by', $request->ended_by);
                })
                ->when($request->filled('from'), function ($q) use ($request) {
                    $q->whereDate('login_at', '>=', $request->from);
                })
                ->when($request->filled('to'), function ($q) use ($request) {
                    $q->whereDate('login_at', '<=', $request->to);
                })
                ->latest()
                ->paginate(20)
                ->withQueryString();

            $sessionLifetimeMinutes = (int) config('session.lifetime', 30);

            $loginLogs->getCollection()->transform(function ($log) use ($sessionLifetimeMinutes) {
                $effectiveEndedBy = $log->ended_by;
                $effectiveLogoutAt = $log->logout_at;

                if (!$log->logout_at && $log->login_at) {
                    $expiryMoment = $log->login_at->copy()->addMinutes($sessionLifetimeMinutes);

                    if (now()->greaterThanOrEqualTo($expiryMoment)) {
                        $effectiveEndedBy = 'expired_or_closed';
                        $effectiveLogoutAt = $expiryMoment;
                    } else {
                        $effectiveEndedBy = 'active';
                    }
                }

                $log->effective_ended_by = $effectiveEndedBy;
                $log->effective_logout_at = $effectiveLogoutAt;

                return $log;
            });
        }

        if ($type === 'timeline') {
            $timelineItems = $this->buildTimeline($request);
        }

        return view('logs.index', compact(
            'type',
            'auditLogs',
            'loginLogs',
            'timelineItems',
            'users'
        ));
    }

    private function buildTimeline(Request $request): Collection
    {
        $auditQuery = AuditLog::with('user')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = trim($request->search);

                $q->where(function ($sub) use ($search) {
                    $sub->where('action', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('target_type', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('action'), function ($q) use ($request) {
                $q->where('action', $request->action);
            })
            ->when($request->filled('user_id'), function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->when($request->filled('from'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from);
            })
            ->when($request->filled('to'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to);
            })
            ->latest()
            ->limit(300)
            ->get()
            ->map(function ($log) {
                return [
                    'source' => 'audit',
                    'sort_at' => $log->created_at,
                    'title' => $this->timelineTitleForAudit($log),
                    'subtitle' => $log->description,
                    'user_name' => $log->user?->name ?? '-',
                    'action' => $log->action,
                    'target_label' => ($log->target_type ? class_basename($log->target_type) : '-') . ($log->target_id ? " #{$log->target_id}" : ''),
                    'old_values' => $log->old_values,
                    'new_values' => $log->new_values,
                    'ip_address' => $log->ip_address,
                    'ended_by' => null,
                    'login_at' => null,
                    'logout_at' => null,
                ];
            });

        $sessionLifetimeMinutes = (int) config('session.lifetime', 30);

        $loginQuery = LoginLog::with('user')
            ->when($request->filled('user_id'), function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            })
            ->when($request->filled('from'), function ($q) use ($request) {
                $q->whereDate('login_at', '>=', $request->from);
            })
            ->when($request->filled('to'), function ($q) use ($request) {
                $q->whereDate('login_at', '<=', $request->to);
            })
            ->latest()
            ->limit(300)
            ->get()
            ->flatMap(function ($log) use ($sessionLifetimeMinutes) {
                $items = [];

                $effectiveEndedBy = $log->ended_by;
                $effectiveLogoutAt = $log->logout_at;

                if (!$log->logout_at && $log->login_at) {
                    $expiryMoment = $log->login_at->copy()->addMinutes($sessionLifetimeMinutes);

                    if (now()->greaterThanOrEqualTo($expiryMoment)) {
                        $effectiveEndedBy = 'expired_or_closed';
                        $effectiveLogoutAt = $expiryMoment;
                    } else {
                        $effectiveEndedBy = 'active';
                    }
                }

                if ($log->login_at) {
                    $items[] = [
                        'source' => 'login',
                        'sort_at' => $log->login_at,
                        'title' => 'تسجيل دخول',
                        'subtitle' => 'تم تسجيل الدخول إلى النظام',
                        'user_name' => $log->user?->name ?? '-',
                        'action' => 'login',
                        'target_label' => 'Session',
                        'old_values' => null,
                        'new_values' => null,
                        'ip_address' => $log->ip_address,
                        'ended_by' => null,
                        'login_at' => $log->login_at,
                        'logout_at' => null,
                    ];
                }

                if ($effectiveLogoutAt) {
                    $items[] = [
                        'source' => 'logout',
                        'sort_at' => $effectiveLogoutAt,
                        'title' => 'تسجيل خروج',
                        'subtitle' => 'انتهاء جلسة المستخدم',
                        'user_name' => $log->user?->name ?? '-',
                        'action' => 'logout',
                        'target_label' => 'Session',
                        'old_values' => null,
                        'new_values' => null,
                        'ip_address' => $log->ip_address,
                        'ended_by' => $effectiveEndedBy,
                        'login_at' => $log->login_at,
                        'logout_at' => $effectiveLogoutAt,
                    ];
                }

                return $items;
            });

        return $auditQuery
            ->concat($loginQuery)
            ->sortByDesc('sort_at')
            ->values();
    }

    private function timelineTitleForAudit(AuditLog $log): string
    {
        return match ($log->action) {
            'user_created' => 'إنشاء مستخدم',
            'user_updated' => 'تعديل مستخدم',
            'user_deleted' => 'حذف مستخدم',
            'user_password_reset' => 'إعادة تعيين كلمة مرور',
            'merchant_created' => 'إنشاء تاجر',
            'merchant_updated' => 'تعديل تاجر',
            'merchant_deleted' => 'حذف تاجر',
            'merchant_inline_updated' => 'تحديث سريع لتاجر',
            default => $log->action,
        };
    }
}
