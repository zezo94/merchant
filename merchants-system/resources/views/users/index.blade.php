@extends('layouts.app')

@php
    $pageTitle = 'إدارة المستخدمين';
@endphp

@push('styles')
    <style>
        .page-card {
            border-radius: 14px;
        }

        .filter-label {
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        .role-badge {
            font-size: 0.85rem;
            border-radius: 999px;
            padding: 6px 10px;
        }
    </style>
@endpush

@section('content')
    @can('manage users')
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="mb-1">إدارة المستخدمين</h2>
                    <p class="text-muted mb-0">إنشاء المستخدمين، تعديل بياناتهم، وتغيير أدوارهم</p>
                </div>

                <a href="{{ route('users.create') }}" class="btn btn-primary">إضافة مستخدم</a>
            </div>

            <form method="GET" action="{{ route('users.index') }}" class="card page-card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="filter-label">بحث</label>
                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                value="{{ request('search') }}"
                                placeholder="الاسم أو البريد الإلكتروني"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="filter-label">الدور</label>
                            <select name="role" class="form-select">
                                <option value="">الكل</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-success w-100">تطبيق</button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary w-100">إعادة ضبط</a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card page-card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">قائمة المستخدمين</span>
                    <span class="badge bg-primary">{{ $users->total() }} مستخدم</span>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>البريد الإلكتروني</th>
                                <th>الدور</th>
                                <th>تاريخ الإنشاء</th>
                                <th>إجراءات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @forelse($user->roles as $role)
                                            <span class="badge bg-dark role-badge">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-muted">بدون دور</span>
                                        @endforelse
                                    </td>
                                    <td>{{ optional($user->created_at)->format('Y-m-d H:i') }}</td>
                                    <td class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">تعديل</a>

                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('هل أنت متأكد من حذف المستخدم؟')"
                                                {{ auth()->id() === $user->id ? 'disabled' : '' }}
                                            >
                                                حذف
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">لا يوجد مستخدمون</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger">ليس لديك صلاحية الوصول إلى إدارة المستخدمين.</div>
    @endcan
@endsection
