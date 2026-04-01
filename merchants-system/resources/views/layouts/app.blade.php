<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'نظام إدارة التجار' }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fb;
            font-family: Tahoma, Arial, sans-serif;
        }

        .app-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
        }

        .app-brand {
            font-weight: 700;
            color: #212529;
            text-decoration: none;
        }

        .app-brand:hover {
            color: #0d6efd;
        }

        .page-shell {
            padding-top: 24px;
            padding-bottom: 32px;
        }

        .app-footer {
            color: #6c757d;
            font-size: 14px;
            text-align: center;
            padding: 18px 0 28px;
        }

        .card {
            border: 0;
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.05);
        }

        .btn {
            border-radius: 10px;
        }

        .form-control,
        .form-select,
        textarea.form-control {
            border-radius: 10px;
        }

        .table {
            background: #fff;
        }

        .navbar-user-name {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 600;
        }
    </style>

    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg app-navbar">
    <div class="container">
        @auth
            <a class="app-brand" href="{{ route('merchants.index') }}">
                نظام إدارة التجار
            </a>
        @else
            <a class="app-brand" href="{{ route('login') }}">
                نظام إدارة التجار
            </a>
        @endauth

        <div class="d-flex align-items-center gap-2 flex-wrap">
            @auth
                @can('view dashboard')
                    <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-dark">لوحة التحكم</a>
                @endcan

                @can('view merchants')
                    <a href="{{ route('merchants.index') }}" class="btn btn-sm btn-outline-secondary">التجار</a>
                @endcan

                @can('create merchants')
                    <a href="{{ route('merchants.create') }}" class="btn btn-sm btn-primary">إضافة تاجر</a>
                @endcan

                @can('manage users')
                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary">المستخدمون</a>
                @endcan

                <a href="{{ route('profile.password.edit') }}" class="btn btn-sm btn-outline-warning">
                    تغيير كلمة المرور
                </a>

                <span class="navbar-user-name ms-2">
                    {{ auth()->user()->name }}
                </span>

                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">تسجيل الخروج</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-primary">تسجيل الدخول</a>
            @endauth
        </div>
    </div>
</nav>

<main class="page-shell">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<footer class="app-footer">
    <div class="container">
        {{ date('Y') }} © نظام إدارة التجار
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>
