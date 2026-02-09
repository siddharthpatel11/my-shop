<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    @php
        $appName = $layoutSettings->frontend_app_name ?? 'MyShop';
    @endphp

    <title>@yield('title', $appName)</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    @if (isset($layoutSettings) && $layoutSettings->frontend_favicon_url)
        <link rel="icon" type="image/x-icon" href="{{ $layoutSettings->frontend_favicon_url }}">
    @endif

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            font-size: 0.75rem;
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .cart-icon-wrapper {
            position: relative;
            display: inline-block;
        }

        .navbar-brand {
            font-size: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar-brand-logo {
            max-height: 45px;
            width: auto;
        }

        .navbar-brand-with-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .navbar-brand-text {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-link {
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: #667eea !important;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .nav-link.active {
            color: #667eea !important;
        }
    </style>

    @stack('styles')
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand-with-logo" href="{{ route('frontend.home') }}">
                @if (isset($layoutSettings) && $layoutSettings->frontend_logo_url)
                    <img src="{{ $layoutSettings->frontend_logo_url }}" alt="{{ $appName }}"
                        class="navbar-brand-logo">
                @else
                    <span class="navbar-brand">
                        <i class="fas fa-store me-2"></i>{{ $appName }}
                    </span>
                @endif
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('frontend.home') ? 'active' : '' }}"
                            href="{{ route('frontend.home') }}">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('frontend.products.*') ? 'active' : '' }}"
                            href="{{ route('frontend.products.index') }}">
                            <i class="fas fa-box-open me-1"></i> Products
                        </a>
                    </li>
                    @auth('customer')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('frontend.orders*') || request()->routeIs('frontend.order.*') ? 'active' : '' }}"
                                href="{{ route('frontend.orders') }}">
                                <i class="fas fa-receipt me-1"></i> My Orders
                            </a>
                        </li>
                    @endauth
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link position-relative {{ request()->routeIs('frontend.cart') || request()->routeIs('checkout.*') ? 'active' : '' }}"
                            href="{{ route('frontend.cart') }}">
                            <div class="cart-icon-wrapper">
                                <i class="fas fa-shopping-cart fa-lg"></i>
                                @auth('customer')
                                    @php
                                        $cartCount = \App\Models\CartItem::where(
                                            'customer_id',
                                            auth('customer')->id(),
                                        )->sum('quantity');
                                    @endphp
                                    @if ($cartCount > 0)
                                        <span class="cart-badge">{{ $cartCount }}</span>
                                    @endif
                                @endauth
                            </div>
                        </a>
                    </li>
                    {{-- Profile Menu --}}
                    @auth('customer')
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle fa-lg me-1"></i>
                                {{ auth('customer')->user()->name ?? 'Account' }}
                            </a>

                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 10px;">
                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.profile') }}">
                                        <i class="fas fa-user me-2"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('frontend.orders') }}">
                                        <i class="fas fa-receipt me-2"></i> My Orders
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('customer.logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item ms-lg-3">
                            <a class="nav-link" href="{{ route('customer.login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-light border-top mt-5">
        <div class="container py-4">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    @if (isset($layoutSettings) && $layoutSettings->footer_text)
                        <p class="text-muted mb-0">{{ $layoutSettings->footer_text }}</p>
                    @else
                        <p class="text-muted mb-0">© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
                    @endif
                </div>
                <div class="col-md-6 text-center text-md-end">
                    @if (isset($layoutSettings) && $layoutSettings->social_links)
                        @if (isset($layoutSettings->social_links['facebook']))
                            <a href="{{ $layoutSettings->social_links['facebook'] }}" target="_blank"
                                class="text-muted text-decoration-none me-3">
                                <i class="fab fa-facebook fa-lg"></i>
                            </a>
                        @endif
                        @if (isset($layoutSettings->social_links['twitter']))
                            <a href="{{ $layoutSettings->social_links['twitter'] }}" target="_blank"
                                class="text-muted text-decoration-none me-3">
                                <i class="fab fa-twitter fa-lg"></i>
                            </a>
                        @endif
                        @if (isset($layoutSettings->social_links['instagram']))
                            <a href="{{ $layoutSettings->social_links['instagram'] }}" target="_blank"
                                class="text-muted text-decoration-none">
                                <i class="fab fa-instagram fa-lg"></i>
                            </a>
                        @endif
                        @if (isset($layoutSettings->social_links['linkedin']))
                            <a href="{{ $layoutSettings->social_links['linkedin'] }}" target="_blank"
                                class="text-muted text-decoration-none ms-3">
                                <i class="fab fa-linkedin fa-lg"></i>
                            </a>
                        @endif
                    @else
                        <a href="#" class="text-muted text-decoration-none me-3">
                            <i class="fab fa-facebook fa-lg"></i>
                        </a>
                        <a href="#" class="text-muted text-decoration-none me-3">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                        <a href="#" class="text-muted text-decoration-none">
                            <i class="fab fa-instagram fa-lg"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Cart Count Update Script --}}
    <script>
        // Update cart count on page load
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('shoppingCart') || '[]');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const cartBadge = document.getElementById('cartCount');

            if (cartBadge) {
                cartBadge.textContent = totalItems;
                cartBadge.style.display = totalItems > 0 ? 'flex' : 'none';
            }
        }

        // Update on page load
        document.addEventListener('DOMContentLoaded', updateCartCount);

        // Update when storage changes (for multi-tab support)
        window.addEventListener('storage', function(e) {
            if (e.key === 'shoppingCart') {
                updateCartCount();
            }
        });

        // Custom event for same-tab updates
        window.addEventListener('cartUpdated', updateCartCount);
    </script>

    @stack('scripts')

</body>

</html>
