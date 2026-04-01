@extends('layouts.app')

@php
    $pageTitle = 'تسجيل الدخول';
@endphp

@push('styles')
    <style>
        .login-card {
            border-radius: 18px;
        }

        .login-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card login-card shadow-sm border-0">
                    <div class="card-header bg-white py-3 text-center">
                        <h4 class="mb-1">تسجيل الدخول</h4>
                        <div class="login-subtitle">ادخل إلى نظام إدارة التجار</div>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('login.submit') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">البريد الإلكتروني</label>
                                <input
                                    type="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                >
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">كلمة المرور</label>
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    required
                                >
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">تذكرني</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                دخول
                            </button>
                        </form>

                        <div class="mt-4 small text-muted">
                            <div>Super Admin: super@admin.com / password123</div>
                            <div>Admin: admin@admin.com / password123</div>
                            <div>User: user@admin.com / password123</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
