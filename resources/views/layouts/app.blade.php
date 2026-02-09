<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $appName = $layoutSettings->admin_app_name ?? config('app.name', 'Laravel CRUD');
        $adminIcon = $layoutSettings->admin_icon ?? 'fas fa-shield-halved';
    @endphp

    <title>@yield('title', $appName)</title>

    <!-- Favicon -->
    @if (isset($layoutSettings) && $layoutSettings->admin_favicon)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $layoutSettings->admin_favicon) }}">
    @endif

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
        }

        /* Top Header Navbar */
        .admin-top-navbar {
            background: linear-gradient(135deg, #64bcef 0%, #b482e7 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
            padding: 10px 0;
        }

        .admin-top-navbar .navbar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .admin-top-navbar .navbar-brand:hover {
            color: white;
        }

        /* Logo Styles */
        .admin-logo-image {
            max-height: 50px;
            max-width: 180px;
            width: auto;
            height: auto;
            object-fit: contain;
            background: white;
            padding: 6px 12px;
            border-radius: 8px;
        }

        .admin-logo-icon {
            font-size: 1.8rem;
            color: white;
        }

        /* Navigation Links */
        .admin-top-navbar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            padding: 8px 16px;
            margin: 0 4px;
            border-radius: 6px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .admin-top-navbar .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        .admin-top-navbar .nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
        }

        .admin-top-navbar .nav-link i {
            font-size: 1rem;
        }

        /* User Dropdown */
        .user-dropdown-toggle {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .user-dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }

        .user-dropdown-toggle::after {
            margin-left: 8px;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            margin-top: 8px;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s;
        }

        .dropdown-item:hover {
            background: #f8f9fa;
            color: #adbcfc;
        }

        .dropdown-item i {
            width: 20px;
            margin-right: 8px;
        }

        /* Main Content */
        .main-content {
            padding: 30px 15px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Mobile Responsive */
        @media (max-width: 991px) {
            .admin-top-navbar .navbar-collapse {
                background: rgba(0, 0, 0, 0.1);
                padding: 15px;
                border-radius: 8px;
                margin-top: 10px;
            }

            .admin-top-navbar .nav-link {
                margin: 4px 0;
            }

            .main-content {
                padding: 20px 10px;
            }
        }

        @media (max-width: 576px) {
            .admin-logo-image {
                max-height: 40px;
                max-width: 140px;
            }

            .admin-top-navbar .navbar-brand {
                font-size: 1.2rem;
            }

            .admin-logo-icon {
                font-size: 1.4rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Top Navigation Bar -->
    <nav class="admin-top-navbar navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid px-3 px-lg-4">
            <!-- Logo/Brand -->
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                @if (isset($layoutSettings) && $layoutSettings->admin_logo)
                    <img src="{{ asset('storage/' . $layoutSettings->admin_logo) }}" alt="{{ $appName }}"
                        class="admin-logo-image">
                @else
                    <i class="{{ $adminIcon }} admin-logo-icon"></i>
                    <span class="d-none d-md-inline">{{ $appName }}</span>
                @endif
            </a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Menu -->
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                            href="{{ route('products.index') }}">
                            <i class="fas fa-box"></i>
                            <span>Products</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                            href="{{ route('categories.index') }}">
                            <i class="fas fa-tags"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sizes.*') ? 'active' : '' }}"
                            href="{{ route('sizes.index') }}">
                            <i class="fas fa-ruler"></i>
                            <span>Sizes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('colors.*') ? 'active' : '' }}"
                            href="{{ route('colors.index') }}">
                            <i class="fas fa-palette"></i>
                            <span>Colors</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('taxes.*') ? 'active' : '' }}"
                            href="{{ route('taxes.index') }}">
                            <i class="fas fa-percentage"></i>
                            <span>Taxes</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('discounts.*') ? 'active' : '' }}"
                            href="{{ route('discounts.index') }}">
                            <i class="fas fa-tag"></i>
                            <span>Discounts</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                            href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Orders</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('layout-settings.*') ? 'active' : '' }}"
                            href="{{ route('layout-settings.index') }}">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('pages.*') ? 'active' : '' }}"
                            href="{{ route('pages.index') }}">
                            <i class="fas fa-file-alt me-1"></i> Pages
                        </a>
                    </li>
                </ul>

                <!-- User Menu -->
                @auth
                    <div class="dropdown">
                        <button class="btn user-dropdown-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-user-circle"></i>
                            {{ auth()->user()->name }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt"></i>
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>
