@extends('layouts.app')

@php
    $pageTitle = 'لوحة التحكم';
@endphp

@push('styles')
    <style>
        .stats-card {
            border-radius: 16px;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .stats-label {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 10px;
        }

        .chart-card {
            border-radius: 16px;
        }

        .section-title {
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
    @can('view dashboard')
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="mb-1">لوحة التحكم</h2>
                    <p class="text-muted mb-0">إحصائيات عامة ورسوم بيانية للنظام</p>
                </div>

                <a href="{{ route('merchants.index') }}" class="btn btn-outline-secondary">الانتقال إلى التجار</a>
            </div>

            {{-- Merchant Stats --}}
            <div class="row g-3 mb-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-label">إجمالي التجار</div>
                            <div class="stats-number">{{ $totalMerchants }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-label">تم التواصل</div>
                            <div class="stats-number text-success">{{ $contacted }}</div>
                            <small>{{ $contactedPercent }}%</small>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-label">تمت الدعوة</div>
                            <div class="stats-number text-primary">{{ $invited }}</div>
                            <small>{{ $invitedPercent }}%</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sensitive System Stats --}}
            @if($canViewSystemStats)
                <div class="row g-3 mb-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="card stats-card">
                            <div class="card-body">
                                <div class="stats-label">عدد المستخدمين</div>
                                <div class="stats-number">{{ $totalUsers }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card stats-card">
                            <div class="card-body">
                                <div class="stats-label">تسجيلات الدخول اليوم</div>
                                <div class="stats-number">{{ $todayLogins }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="card stats-card">
                            <div class="card-body">
                                <div class="stats-label">النشاطات اليوم</div>
                                <div class="stats-number">{{ $todayActivities }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Charts --}}
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card chart-card">
                        <div class="card-header bg-white fw-bold">Contacted vs Not Contacted</div>
                        <div class="card-body">
                            <canvas id="contactedChart" height="220"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card chart-card">
                        <div class="card-header bg-white fw-bold">Invited vs Not Invited</div>
                        <div class="card-body">
                            <canvas id="invitedChart" height="220"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card chart-card">
                        <div class="card-header bg-white fw-bold">نشاط الأيام</div>
                        <div class="card-body">
                            <canvas id="activityChart" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Root / System Stats Only --}}
            @if($canViewSystemStats)
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card chart-card">
                            <div class="card-header bg-white section-title">أكثر مستخدم نشاطًا</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead>
                                        <tr>
                                            <th>المستخدم</th>
                                            <th>العدد</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($mostActiveUsers as $row)
                                            <tr>
                                                <td>{{ $row->user?->name ?? '-' }}</td>
                                                <td>{{ $row->total_actions }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">لا توجد بيانات</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card chart-card">
                            <div class="card-header bg-white section-title">أكثر تعديلًا</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead>
                                        <tr>
                                            <th>المستخدم</th>
                                            <th>العدد</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($mostEditingUsers as $row)
                                            <tr>
                                                <td>{{ $row->user?->name ?? '-' }}</td>
                                                <td>{{ $row->total_edits }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">لا توجد بيانات</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card chart-card">
                            <div class="card-header bg-white section-title">سجلات خطيرة</div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle">
                                        <thead>
                                        <tr>
                                            <th>المستخدم</th>
                                            <th>العملية</th>
                                            <th>الوقت</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($dangerLogs as $log)
                                            <tr>
                                                <td>{{ $log->user?->name ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-danger">{{ $log->action }}</span>
                                                </td>
                                                <td>{{ $log->created_at }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">لا توجد بيانات</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="alert alert-danger">ليس لديك صلاحية الوصول إلى لوحة التحكم.</div>
    @endcan
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const contactedCtx = document.getElementById('contactedChart');
            if (contactedCtx) {
                new Chart(contactedCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Contacted', 'Not Contacted'],
                        datasets: [{
                            data: [{{ $contacted }}, {{ $notContacted }}]
                        }]
                    }
                });
            }

            const invitedCtx = document.getElementById('invitedChart');
            if (invitedCtx) {
                new Chart(invitedCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Invited', 'Not Invited'],
                        datasets: [{
                            data: [{{ $invited }}, {{ $notInvited }}]
                        }]
                    }
                });
            }

            const activityCtx = document.getElementById('activityChart');
            if (activityCtx) {
                new Chart(activityCtx, {
                    type: 'line',
                    data: {
                        labels: @json($activityLabels),
                        datasets: [{
                            label: 'Activity',
                            data: @json($activityValues),
                            tension: 0.3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
        });
    </script>
@endpush
