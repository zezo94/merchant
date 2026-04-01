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
    </style>
@endpush

@section('content')
    @can('view dashboard')
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="mb-1">لوحة التحكم</h2>
                    <p class="text-muted mb-0">إحصائيات عامة عن نظام إدارة التجار</p>
                </div>

                <a href="{{ route('merchants.index') }}" class="btn btn-outline-secondary">الانتقال إلى التجار</a>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-label">إجمالي التجار</div>
                            <div class="stats-number">{{ $stats['total_merchants'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-label">تم التواصل</div>
                            <div class="stats-number text-success">{{ $stats['contacted_merchants'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-label">تمت دعوتهم</div>
                            <div class="stats-number text-primary">{{ $stats['invited_merchants'] }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="stats-label">بدون ملاحظات</div>
                            <div class="stats-number text-danger">{{ $stats['without_notes'] }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card stats-card">
                        <div class="card-header bg-white fw-bold">
                            آخر 10 تجار
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>اسم الشركة</th>
                                        <th>القطاع</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($stats['latest_merchants'] as $merchant)
                                        <tr>
                                            <td>{{ $merchant->id }}</td>
                                            <td>{{ $merchant->organization_name }}</td>
                                            <td>{{ $merchant->sector ?: '-' }}</td>
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

                <div class="col-lg-6">
                    <div class="card stats-card">
                        <div class="card-header bg-white fw-bold">
                            أكثر القطاعات
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                    <tr>
                                        <th>القطاع</th>
                                        <th>العدد</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($stats['sector_counts'] as $row)
                                        <tr>
                                            <td>{{ $row->sector ?: 'غير محدد' }}</td>
                                            <td>{{ $row->total }}</td>
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
            </div>
        </div>
    @else
        <div class="alert alert-danger">ليس لديك صلاحية الوصول إلى لوحة التحكم.</div>
    @endcan
@endsection
