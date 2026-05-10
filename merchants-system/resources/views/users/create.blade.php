@extends('layouts.app')

@php
    $pageTitle = 'إضافة مستخدم';
@endphp

@section('body_class', 'app-body users-create-page')

@section('content')
    @can('manage users')
        @php
            $isRoot = (bool) auth()->user()?->is_root;
        @endphp

        <section class="users-create-section">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="mb-1">إضافة مستخدم</h2>
                    <p class="text-muted mb-0">إنشاء حساب جديد وتحديد الدور المناسب له</p>
                </div>

                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">رجوع</a>
            </div>

            @include('users.partials.user-form-card', [
                'mode' => 'create',
                'userModel' => null,
                'roles' => $roles,
                'isRoot' => $isRoot,
                'formAction' => route('users.store'),
                'formMethod' => 'POST',
                'submitLabel' => 'حفظ',
                'cancelRoute' => route('users.index'),
            ])
        </section>
    @else
        <div class="alert alert-danger">ليس لديك صلاحية إضافة مستخدمين.</div>
    @endcan
@endsection
