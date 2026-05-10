<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\LoginLog;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Merchant Stats - تظهر لكل من لديه view dashboard
        |--------------------------------------------------------------------------
        */
        $totalMerchants = Merchant::count();

        $contacted = Merchant::where('contacted', 1)->count();
        $notContacted = Merchant::where(function ($q) {
            $q->where('contacted', 0)->orWhereNull('contacted');
        })->count();

        $invited = Merchant::where('invited', 1)->count();
        $notInvited = Merchant::where(function ($q) {
            $q->where('invited', 0)->orWhereNull('invited');
        })->count();

        $contactedPercent = $totalMerchants > 0
            ? round(($contacted / $totalMerchants) * 100)
            : 0;

        $invitedPercent = $totalMerchants > 0
            ? round(($invited / $totalMerchants) * 100)
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Activity Chart - عام
        |--------------------------------------------------------------------------
        */
        $activityLabels = [];
        $activityValues = [];

        $activityByDaysRaw = AuditLog::selectRaw('DATE(created_at) as log_date, COUNT(*) as total')
            ->whereDate('created_at', '>=', now()->subDays(6)->toDateString())
            ->groupBy('log_date')
            ->orderBy('log_date')
            ->get()
            ->keyBy('log_date');

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $activityLabels[] = $date;
            $activityValues[] = (int) ($activityByDaysRaw[$date]->total ?? 0);
        }

        /*
        |--------------------------------------------------------------------------
        | Sensitive System Stats - فقط root أو من عنده صلاحية خاصة
        |--------------------------------------------------------------------------
        */
        $canViewSystemStats = (bool) auth()->user()?->is_root
            || auth()->user()?->can('view system stats');

        $totalUsers = null;
        $todayLogins = null;
        $todayActivities = null;

        $mostActiveUsers = collect();
        $mostEditingUsers = collect();
        $dangerLogs = collect();

        if ($canViewSystemStats) {
            $totalUsers = User::count();

            $todayLogins = LoginLog::whereDate('login_at', today())->count();

            $todayActivities = AuditLog::whereDate('created_at', today())->count();

            $mostActiveUsers = AuditLog::with('user')
                ->select('user_id', DB::raw('COUNT(*) as total_actions'))
                ->whereNotNull('user_id')
                ->groupBy('user_id')
                ->orderByDesc('total_actions')
                ->take(5)
                ->get();

            $mostEditingUsers = AuditLog::with('user')
                ->select('user_id', DB::raw('COUNT(*) as total_edits'))
                ->whereNotNull('user_id')
                ->whereIn('action', [
                    'user_updated',
                    'merchant_updated',
                    'merchant_inline_updated',
                    'user_password_reset',
                ])
                ->groupBy('user_id')
                ->orderByDesc('total_edits')
                ->take(5)
                ->get();

            $dangerLogs = AuditLog::with('user')
                ->whereIn('action', [
                    'user_deleted',
                    'merchant_deleted',
                    'user_password_reset',
                ])
                ->latest()
                ->take(10)
                ->get();
        }

        return view('dashboard.index', compact(
            'totalMerchants',
            'contacted',
            'notContacted',
            'invited',
            'notInvited',
            'contactedPercent',
            'invitedPercent',
            'activityLabels',
            'activityValues',
            'canViewSystemStats',
            'totalUsers',
            'todayLogins',
            'todayActivities',
            'mostActiveUsers',
            'mostEditingUsers',
            'dangerLogs'
        ));
    }
}
