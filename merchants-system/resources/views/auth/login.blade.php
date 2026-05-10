@extends('layouts.app')

@php
    $pageTitle = 'Login';
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

        .password-input-group {
            position: relative;
        }

        .password-toggle-btn {
            position: absolute;
            inset-inline-end: 0.5rem;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #6c757d;
            font-size: 0.9rem;
            padding: 0.25rem 0.5rem;
            cursor: pointer;
        }

        .password-toggle-btn:hover {
            color: #212529;
        }

        .password-field {
            padding-inline-end: 4.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card login-card shadow-sm border-0">
                    <div class="card-header bg-white py-3 text-center">
                        <h4 class="mb-1">Login</h4>
                        <div class="login-subtitle">Merchants MS</div>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('login.submit') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">E-Mail</label>
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
                                <label class="form-label">Password</label>

                                <div class="password-input-group">
                                    <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        class="form-control password-field @error('password') is-invalid @enderror"
                                        required
                                    >

                                    <button
                                        type="button"
                                        class="password-toggle-btn"
                                        id="togglePassword"
                                        aria-label="Toggle password visibility"
                                    >
                                        Show
                                    </button>
                                </div>

                                @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="form-check-input"
                                    id="remember"
                                    value="1"
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Login
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('togglePassword');

            if (!passwordInput || !toggleBtn) return;

            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';

                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleBtn.textContent = isPassword ? 'Hide' : 'Show';
            });
        });
    </script>
@endsection
