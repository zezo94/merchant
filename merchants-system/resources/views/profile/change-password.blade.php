@extends('layouts.app')

@php
    $pageTitle = 'تغيير كلمة المرور';
@endphp

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h2 class="mb-1">تغيير كلمة المرور</h2>
                <p class="text-muted mb-0">يمكنك تحديث كلمة مرور حسابك الحالي من هنا</p>
            </div>

            <a href="{{ route('merchants.index') }}" class="btn btn-outline-secondary">رجوع</a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white fw-bold">
                        تحديث كلمة المرور
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="form-label">كلمة المرور الحالية</label>
                                <input
                                    type="password"
                                    name="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    required
                                >
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">كلمة المرور الجديدة</label>
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

                            <div class="mb-3">
                                <label class="form-label">تأكيد كلمة المرور الجديدة</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control"
                                    required
                                >
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <button type="submit" class="btn btn-success">
                                    حفظ كلمة المرور الجديدة
                                </button>

                                <a href="{{ route('merchants.index') }}" class="btn btn-secondary">
                                    إلغاء
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mt-3">
                    <div class="card-body">
                        <div class="small text-muted">
                            تأكد أن كلمة المرور الجديدة:
                        </div>
                        <ul class="mb-0 mt-2">
                            <li>لا تقل عن 6 أحرف</li>
                            <li>ليست سهلة التخمين</li>
                            <li>تتذكرها بسهولة</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
