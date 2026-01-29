<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel CRUD') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Custom Styles Stack -->
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="{{ route('products.index') }}">Laravel CRUD</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                            href="{{ route('products.index') }}">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                            href="{{ route('categories.index') }}">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('sizes.*') ? 'active' : '' }}"
                            href="{{ route('sizes.index') }}">Sizes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('colors.*') ? 'active' : '' }}"
                            href="{{ route('colors.index') }}">Colors</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('colors.*') ? 'active' : '' }}"
                            href="{{ route('taxes.create') }}">Taxs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('discounts.*') ? 'active' : '' }}"
                            href="{{ route('discounts.index') }}">Discounts</a>
                    </li>
                </ul>

                <!-- User Dropdown -->
                @auth
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Register</a>
                            </li>
                        @endguest

                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    {{ auth()->user()->name }}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('products.index') }}">
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button class="dropdown-item">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endauth
                    </ul>

                @endauth
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <!-- jQuery (Required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts Stack -->
    @stack('scripts')
</body>

</html>
