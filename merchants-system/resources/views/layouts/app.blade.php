<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ isset($pageTitle) && filled($pageTitle) ? $pageTitle . ' | نظام إدارة التجار' : 'نظام إدارة التجار' }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'نظام إدارة التجار')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="@yield('body_class', 'app-body')">
<div class="app-wrapper">
    @include('partials.app-navbar')

    <main class="page-shell py-4">
        <div class="container">
            @include('partials.app-alerts')

            @yield('content')
        </div>
    </main>

    @include('partials.app-footer')
</div>

@stack('scripts')
</body>
</html>
