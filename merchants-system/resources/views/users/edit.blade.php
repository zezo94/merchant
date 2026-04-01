@extends('layouts.app')

@php
    $pageTitle = 'تعديل مستخدم';
@endphp

@push('styles')
    <style>
        .page-card {
            border-radius: 14px;
        }

        .section-title {
            font-weight: 700;
            margin-bottom: 0;
        }
    </style>
@endpush

@section('content')
    @can('manage users')
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="mb-1">تعديل مستخدم</h2>
                    <p class="text-muted mb-0">تعديل البيانات الأساسية أو كلمة المرور أو الدور</p>
                </div>

                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">رجوع</a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>يوجد أخطاء في الإدخال:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card page-card">
                <div class="card-header bg-white">
                    <h5 class="section-title">بيانات المستخدم</h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">الاسم</label>
                                <input
                                    type="text"
                                    name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}"
                                    required
                                >
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">البريد الإلكتروني</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}"
                                    required
                                >
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">كلمة المرور الجديدة</label>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                >
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">اتركها فارغة إذا لا تريد تغييرها</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تأكيد كلمة المرور</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control"
                                >
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الدور</label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">اختر الدور</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-success">حفظ التعديلات</button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-danger">ليس لديك صلاحية تعديل المستخدمين.</div>
    @endcan
@endsection
