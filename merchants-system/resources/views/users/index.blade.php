@extends('layouts.app')

@php
    $pageTitle = 'إدارة المستخدمين';
@endphp

@section('body_class', 'app-body users-index-page')

@section('content')
    @can('manage users')
        @php
            $isRoot = (bool) auth()->user()?->is_root;
        @endphp

        <section class="users-index-section">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="mb-1">إدارة المستخدمين</h2>
                    <p class="text-muted mb-0">إنشاء المستخدمين، تعديل بياناتهم، تغيير أدوارهم، وإعادة تعيين كلمات المرور</p>
                </div>

                <a href="{{ route('users.create') }}" class="btn btn-primary">إضافة مستخدم</a>
            </div>

            @include('users.partials.user-index-filters-card', [
                'roles' => $roles,
            ])

            @include('users.partials.user-index-table', [
                'users' => $users,
                'isRoot' => $isRoot,
            ])
        </section>
    @else
        <div class="alert alert-danger">ليس لديك صلاحية الوصول إلى إدارة المستخدمين.</div>
    @endcan
@endsection
