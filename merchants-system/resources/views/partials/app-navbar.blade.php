<nav class="navbar navbar-expand-lg app-navbar">
    <div class="container app-navbar-container">
        <div class="app-navbar-topbar w-100 d-flex align-items-center justify-content-between">
            <button
                class="navbar-toggler app-navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar"
                aria-controls="mainNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <a
                class="app-brand fw-bold text-decoration-none"
                href="{{ auth()->check() ? route('merchants.index') : route('login') }}"
            >
                <span class="app-brand-text">Merchants DMS</span>
            </a>
        </div>

        <div class="collapse navbar-collapse app-navbar-collapse" id="mainNavbar">
            @auth
                <div class="app-navbar-mobile-user-card d-lg-none">
                    <div class="app-mobile-user-head">
                        <div>
                            <div class="app-mobile-user-name">{{ auth()->user()?->name ?? 'User' }}</div>
                            <div class="app-mobile-user-role">
                                {{ auth()->user()?->is_root ? 'Root Administrator' : 'Authenticated User' }}
                            </div>
                        </div>

                        @if(auth()->user()?->is_root)
                            <span class="badge bg-warning text-dark">ROOT</span>
                        @else
                            <span class="badge bg-secondary">USER</span>
                        @endif
                    </div>
                </div>
            @endauth

            <div class="app-navbar-main-row">
                @auth
                    <div class="app-navbar-user-area d-none d-lg-flex align-items-center gap-2">
                        <div class="dropdown">
                            <button
                                class="btn btn-sm app-user-dropdown-btn dropdown-toggle d-flex align-items-center gap-2"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                @if(auth()->user()?->is_root)
                                    <span class="badge bg-warning text-dark">ROOT</span>
                                @else
                                    <span class="badge bg-secondary">USER</span>
                                @endif

                                <span class="navbar-user-name">
                                    {{ auth()->user()?->name ?? 'User' }}
                                </span>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-start app-user-dropdown-menu">
                                <li>
                                    <div class="dropdown-item-text app-user-dropdown-header">
                                        <div class="fw-bold">{{ auth()->user()?->name ?? 'User' }}</div>
                                        <div class="small text-muted">
                                            {{ auth()->user()?->is_root ? 'Root Administrator' : 'Authenticated User' }}
                                        </div>
                                    </div>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.password.edit') }}">
                                        Change Password
                                    </a>
                                </li>

                                @if(auth()->user()?->is_root)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logs.index') }}">
                                            Logs
                                        </a>
                                    </li>
                                @endif

                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <input type="hidden" name="ended_by" value="manual">
                                        <button type="submit" class="dropdown-item text-danger">
                                            Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endauth

                <div class="app-navbar-spacer d-none d-lg-block"></div>

                <div class="app-navbar-nav d-flex align-items-center flex-wrap gap-2">
                    @auth
                        @if(auth()->user()?->is_root)
                            <a href="{{ route('logs.index') }}" class="btn btn-sm btn-warning">
                                Logs
                            </a>
                        @endif

                        @can('manage users')
                            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary">
                                Users
                            </a>
                        @endcan

                        @can('create merchants')
                            <a href="{{ route('merchants.create') }}" class="btn btn-sm btn-primary">
                                Create +
                            </a>
                        @endcan

                        @can('view merchants')
                            <a href="{{ route('merchants.index') }}" class="btn btn-sm btn-outline-secondary">
                                Merchants
                            </a>
                        @endcan

                        @can('view dashboard')
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-dark">
                                Dashboard
                            </a>
                        @endcan

                        <a href="{{ route('profile.password.edit') }}" class="btn btn-sm btn-outline-warning d-lg-none">
                            Change Password
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="d-lg-none w-100">
                            @csrf
                            <input type="hidden" name="ended_by" value="manual">
                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                Logout
                            </button>
                        </form>
                    @else
                        <span class="navbar-text text-muted small">Guest</span>

                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>
