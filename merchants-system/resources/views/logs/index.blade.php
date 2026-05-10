@extends('layouts.app')

@php
    $pageTitle = 'Logs';

    $actionColors = [
        'login' => 'success',
        'logout' => 'secondary',
        'user_created' => 'primary',
        'user_updated' => 'warning',
        'user_deleted' => 'danger',
        'user_password_reset' => 'dark',
        'merchant_created' => 'primary',
        'merchant_updated' => 'warning',
        'merchant_deleted' => 'danger',
        'merchant_inline_updated' => 'info',
    ];

    $sessionColors = [
        'manual' => 'secondary',
        'timeout' => 'warning',
        'forced' => 'danger',
        'active' => 'success',
        'expired_or_closed' => 'dark',
    ];
@endphp

@section('body_class', 'app-body logs-page')

@section('content')
    <section class="logs-page-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h1 class="h3 mb-1">Logs System</h1>
                <p class="text-muted mb-0">Root timeline + audit + login logs</p>
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap mb-4">
            <a href="{{ route('logs.index', array_merge(request()->except('page', 'type'), ['type' => 'timeline'])) }}"
               class="btn {{ $type === 'timeline' ? 'btn-primary' : 'btn-outline-primary' }}">
                Timeline
            </a>

            <a href="{{ route('logs.index', array_merge(request()->except('page', 'type'), ['type' => 'audit'])) }}"
               class="btn {{ $type === 'audit' ? 'btn-primary' : 'btn-outline-primary' }}">
                Audit Logs
            </a>

            <a href="{{ route('logs.index', array_merge(request()->except('page', 'type'), ['type' => 'login'])) }}"
               class="btn {{ $type === 'login' ? 'btn-primary' : 'btn-outline-primary' }}">
                Login Logs
            </a>
        </div>

        @if(in_array($type, ['timeline', 'audit']))
            <div class="card page-card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('logs.index') }}" class="logs-filter-form">
                        <input type="hidden" name="type" value="{{ $type }}">

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label filter-label">Search</label>
                                <input
                                    id="search"
                                    type="text"
                                    name="search"
                                    class="form-control"
                                    value="{{ request('search') }}"
                                    placeholder="action / description / target"
                                >
                            </div>

                            <div class="col-md-2">
                                <label for="action" class="form-label filter-label">Action</label>
                                <input
                                    id="action"
                                    type="text"
                                    name="action"
                                    class="form-control"
                                    value="{{ request('action') }}"
                                    placeholder="مثال: merchant_updated"
                                >
                            </div>

                            <div class="col-md-3">
                                <label for="user_id" class="form-label filter-label">User</label>
                                <select id="user_id" name="user_id" class="form-select">
                                    <option value="">الكل</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ (string) request('user_id') === (string) $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="from" class="form-label filter-label">From</label>
                                <input
                                    id="from"
                                    type="date"
                                    name="from"
                                    class="form-control"
                                    value="{{ request('from') }}"
                                >
                            </div>

                            <div class="col-md-2">
                                <label for="to" class="form-label filter-label">To</label>
                                <input
                                    id="to"
                                    type="date"
                                    name="to"
                                    class="form-control"
                                    value="{{ request('to') }}"
                                >
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap mt-3">
                            <button type="submit" class="btn btn-success">تطبيق</button>
                            <a href="{{ route('logs.index', ['type' => $type]) }}" class="btn btn-secondary">إعادة ضبط</a>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if($type === 'login')
            <div class="card page-card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('logs.index') }}" class="logs-filter-form">
                        <input type="hidden" name="type" value="login">

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="login_user_id" class="form-label filter-label">User</label>
                                <select id="login_user_id" name="user_id" class="form-select">
                                    <option value="">الكل</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ (string) request('user_id') === (string) $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="ended_by" class="form-label filter-label">Ended By</label>
                                <select id="ended_by" name="ended_by" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="manual" {{ request('ended_by') === 'manual' ? 'selected' : '' }}>manual</option>
                                    <option value="timeout" {{ request('ended_by') === 'timeout' ? 'selected' : '' }}>timeout</option>
                                    <option value="forced" {{ request('ended_by') === 'forced' ? 'selected' : '' }}>forced</option>
                                    <option value="active" {{ request('ended_by') === 'active' ? 'selected' : '' }}>active</option>
                                    <option value="expired_or_closed" {{ request('ended_by') === 'expired_or_closed' ? 'selected' : '' }}>expired_or_closed</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="login_from" class="form-label filter-label">From</label>
                                <input
                                    id="login_from"
                                    type="date"
                                    name="from"
                                    class="form-control"
                                    value="{{ request('from') }}"
                                >
                            </div>

                            <div class="col-md-3">
                                <label for="login_to" class="form-label filter-label">To</label>
                                <input
                                    id="login_to"
                                    type="date"
                                    name="to"
                                    class="form-control"
                                    value="{{ request('to') }}"
                                >
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap mt-3">
                            <button type="submit" class="btn btn-success">تطبيق</button>
                            <a href="{{ route('logs.index', ['type' => 'login']) }}" class="btn btn-secondary">إعادة ضبط</a>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        @if($type === 'timeline')
            <div class="logs-timeline">
                @forelse($timelineItems as $item)
                    @php
                        $color = $actionColors[$item['action']] ?? 'dark';
                    @endphp

                    <div class="logs-timeline-item">
                        <div class="logs-timeline-dot bg-{{ $color }}"></div>

                        <div class="card page-card logs-timeline-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <h5 class="mb-1">{{ $item['title'] }}</h5>
                                        <div class="text-muted small mb-2">{{ $item['subtitle'] }}</div>
                                    </div>

                                    <div class="text-end">
                                        <span class="badge bg-{{ $color }} badge-soft">{{ $item['action'] }}</span>
                                        <div class="small text-muted mt-1">{{ $item['sort_at'] }}</div>
                                    </div>
                                </div>

                                <div class="row g-3 mt-1">
                                    <div class="col-md-3">
                                        <div class="small text-muted">المستخدم</div>
                                        <div>{{ $item['user_name'] }}</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="small text-muted">الهدف</div>
                                        <div>{{ $item['target_label'] }}</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="small text-muted">IP</div>
                                        <div>{{ $item['ip_address'] ?? '-' }}</div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="small text-muted">Session End</div>
                                        <div>
                                            @if(!empty($item['ended_by']))
                                                <span class="badge bg-{{ $sessionColors[$item['ended_by']] ?? 'secondary' }} badge-soft">
                                                    {{ $item['ended_by'] }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>

                                    @if(!empty($item['old_values']))
                                        <div class="col-md-6">
                                            <div class="small text-muted mb-1">Old Values</div>
                                            <pre class="json-box">{{ json_encode($item['old_values'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    @endif

                                    @if(!empty($item['new_values']))
                                        <div class="col-md-6">
                                            <div class="small text-muted mb-1">New Values</div>
                                            <pre class="json-box">{{ json_encode($item['new_values'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    @endif

                                    @if(!empty($item['login_at']) || !empty($item['logout_at']))
                                        <div class="col-md-6">
                                            <div class="small text-muted">Login At</div>
                                            <div>{{ $item['login_at'] ?? '-' }}</div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="small text-muted">Logout At</div>
                                            <div>{{ $item['logout_at'] ?? '-' }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-secondary mb-0">لا توجد أحداث في الـ timeline.</div>
                @endforelse
            </div>
        @endif

        @if($type === 'audit')
            <div class="card page-card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="fw-bold">Audit Logs</span>
                    <span class="badge bg-primary">{{ $auditLogs->total() }} سجل</span>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Target</th>
                            <th>Description</th>
                            <th>Old</th>
                            <th>New</th>
                            <th>IP</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($auditLogs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->user?->name ?? '-' }}</td>
                                <td>
                                        <span class="badge bg-{{ $actionColors[$log->action] ?? 'dark' }} badge-soft">
                                            {{ $log->action }}
                                        </span>
                                </td>
                                <td>
                                    {{ $log->target_type ? class_basename($log->target_type) : '-' }}
                                    @if($log->target_id)
                                        #{{ $log->target_id }}
                                    @endif
                                </td>
                                <td>{{ $log->description }}</td>
                                <td>
                                    <pre class="json-box">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </td>
                                <td>
                                    <pre class="json-box">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </td>
                                <td>{{ $log->ip_address }}</td>
                                <td>{{ $log->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No logs</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $auditLogs->links() }}
                    </div>
                </div>
            </div>
        @endif

        @if($type === 'login')
            <div class="card page-card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="fw-bold">Login Logs</span>
                    <span class="badge bg-primary">{{ $loginLogs->total() }} سجل</span>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Login</th>
                            <th>Logout</th>
                            <th>مدة الجلسة</th>
                            <th>Ended By</th>
                            <th>IP</th>
                            <th>Session</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($loginLogs as $log)
                            @php
                                $endedBy = $log->effective_ended_by ?? $log->ended_by ?? '-';
                                $logoutAt = $log->effective_logout_at ?? $log->logout_at;
                            @endphp

                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->user?->name ?? '-' }}</td>
                                <td>{{ $log->login_at }}</td>
                                <td>{{ $logoutAt ?? '-' }}</td>
                                <td>
                                    @if($log->login_at && $logoutAt)
                                        {{ $log->login_at->diffForHumans($logoutAt, true) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                        <span class="badge bg-{{ $sessionColors[$endedBy] ?? 'secondary' }} badge-soft">
                                            {{ $endedBy }}
                                        </span>
                                </td>
                                <td>{{ $log->ip_address }}</td>
                                <td class="small">{{ $log->session_id }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No logs</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $loginLogs->links() }}
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection
