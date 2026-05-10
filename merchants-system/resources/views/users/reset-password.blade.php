@extends('layouts.app')

@php
    $pageTitle = 'Reset Password';
@endphp

@section('body_class', 'app-body users-reset-password-page')

@section('content')
    @can('manage users')
        <section class="users-reset-password-section">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="mb-1">Reset Password</h2>
                    <p class="text-muted mb-0">إعادة تعيين كلمة مرور المستخدم: {{ $user->name }}</p>
                </div>

                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">رجوع</a>
            </div>

            @include('users.partials.user-reset-password-form-card', [
                'user' => $user,
            ])
        </section>
    @else
        <div class="alert alert-danger">ليس لديك صلاحية إعادة تعيين كلمات المرور.</div>
    @endcan
@endsection
