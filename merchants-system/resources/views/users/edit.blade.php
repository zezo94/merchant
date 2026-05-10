@extends('layouts.app')

@php
    $pageTitle = 'تعديل مستخدم';
@endphp

@section('body_class', 'app-body users-edit-page')

@section('content')
    @can('manage users')
        @php
            $isRoot = (bool) auth()->user()?->is_root;
            $canEditThisUser = $isRoot ? true : !$user->hasRole('super-admin');
        @endphp

        <section class="users-edit-section">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="mb-1">تعديل مستخدم</h2>
                    <p class="text-muted mb-0">تعديل البيانات الأساسية أو كلمة المرور أو الدور</p>
                </div>

                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">رجوع</a>
            </div>

            @if(!$canEditThisUser)
                <div class="alert alert-danger">
                    لا يمكن تعديل مستخدم super-admin.
                </div>
            @else
                @include('users.partials.user-form-card', [
                    'mode' => 'edit',
                    'userModel' => $user,
                    'roles' => $roles,
                    'isRoot' => $isRoot,
                    'formAction' => route('users.update', $user->id),
                    'formMethod' => 'PUT',
                    'submitLabel' => 'حفظ التعديلات',
                    'cancelRoute' => route('users.index'),
                ])
            @endif
        </section>
    @else
        <div class="alert alert-danger">ليس لديك صلاحية تعديل المستخدمين.</div>
    @endcan
@endsection
