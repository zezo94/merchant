@extends('layouts.app')

@php
    $pageTitle = 'إدارة التكرار';
@endphp

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">إدارة السجلات المكررة</h2>
                <p class="text-muted mb-0">عرض المجموعات المكررة حسب نوع المفتاح</p>
            </div>
            <a href="{{ route('merchants.index') }}" class="btn btn-secondary">رجوع للتجار</a>
        </div>

        <form method="GET" action="{{ route('duplicates.index') }}" class="card mb-4">
            <div class="card-body row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">نوع التكرار</label>
                    <select name="type" class="form-select">
                        <option value="membership_no" {{ $type === 'membership_no' ? 'selected' : '' }}>رقم العضوية</option>
                        <option value="commercial_reg_no" {{ $type === 'commercial_reg_no' ? 'selected' : '' }}>رقم السجل التجاري</option>
                        <option value="org_national_no" {{ $type === 'org_national_no' ? 'selected' : '' }}>الرقم الوطني</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary">عرض</button>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-header bg-white fw-bold">مجموعات التكرار</div>
            <div class="card-body">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>القيمة</th>
                        <th>عدد السجلات</th>
                        <th>إجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($duplicates as $row)
                        <tr>
                            <td>{{ $row->{$type} }}</td>
                            <td>{{ $row->duplicates_count }}</td>
                            <td>
                                <a href="{{ route('duplicates.show', ['type' => $type, 'value' => $row->{$type}]) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    عرض السجلات
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">لا يوجد تكرار</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $duplicates->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
