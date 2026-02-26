<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    @php
        $layoutSettings = $layoutSettings ?? new \App\Models\LayoutSetting();
        $appName = $layoutSettings->frontend_app_name ?? config('app.name', 'MyShop');
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

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            max-height: {{ $layoutSettings->logo_size ?? 45 }}px;
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
            background-color: {{ $layoutSettings->title_bg_color ?? '#ffffff' }} !important;
        }

        .navbar .nav-link,
        .navbar-brand-text {
            color: {{ $layoutSettings->title_text_color ?? '#212529' }} !important;
        }

        footer {
            background-color: {{ $layoutSettings->footer_bg_color ?? '#f8f9fa' }} !important;
            color: {{ $layoutSettings->footer_text_color ?? '#6c757d' }} !important;
        }

        footer .text-muted,
        footer a.text-muted {
            color: {{ $layoutSettings->footer_text_color ?? '#6c757d' }} !important;
        }

        .nav-link.active {
            color: #667eea !important;
            border-bottom: 2px solid #667eea;
        }

        /* Footer Improvements */
        footer h6 {
            color: {{ $layoutSettings->footer_text_color ?? '#212529' }} !important;
            font-size: 1rem;
            letter-spacing: 0.5px;
        }

        footer .hover-link {
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        footer .hover-link:hover {
            color: #667eea !important;
            padding-left: 5px;
        }

        footer .social-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(102, 126, 234, 0.1);
            transition: all 0.3s ease;
        }

        footer .social-icon:hover {
            background: #667eea;
            color: white !important;
            transform: translateY(-3px);
        }

        footer .map-container {
            border: 2px solid rgba(102, 126, 234, 0.2);
        }

        footer .contact-info i {
            min-width: 20px;
        }

        .no-caret::after {
            display: none !important;
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
                        <i class="{{ $layoutSettings->frontend_icon ?? 'fas fa-store' }} me-2"></i>
                        <span class="navbar-brand-text">{{ $layoutSettings->site_title ?? $appName }}</span>
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
                    {{-- Dynamic Menu Items --}}
                    @if (isset($layoutSettings) && $layoutSettings->menu_items)
                        @foreach ($layoutSettings->menu_items as $item)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ $item['url'] ?? '#' }}">
                                    {{ $item['label'] ?? '' }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                    @php
                        $dynamicPages = \App\Models\Page::all();
                    @endphp
                    @if ($dynamicPages->count() > 0)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPages" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-file-alt me-1"></i> Pages
                            </a>
                            <ul class="dropdown-menu shadow-sm" aria-labelledby="navbarDropdownPages">
                                @foreach ($dynamicPages as $page)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('page.show', $page->slug) }}">
                                            {{ $page->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                    @auth('customer')
                        {{-- My Panel Consolidated into Profile --}}
                    @endauth
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link position-relative {{ request()->routeIs('frontend.cart') || request()->routeIs('checkout.*') ? 'active' : '' }}"
                            href="{{ route('frontend.cart') }}" title="Cart">
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
                    @auth('customer')
                        <li class="nav-item dropdown ms-lg-3">
                            <a class="nav-link position-relative dropdown-toggle no-caret" href="#"
                                id="wishlistDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                                title="Wishlist">
                                <div class="cart-icon-wrapper">
                                    <i class="fas fa-heart fa-lg text-danger"></i>
                                    @php
                                        $wishlistCount = \App\Models\Wishlist::where(
                                            'customer_id',
                                            auth('customer')->id(),
                                        )->count();
                                        $wishlistItemsPreview = \App\Models\Wishlist::with('product')
                                            ->where('customer_id', auth('customer')->id())
                                            ->latest()
                                            ->take(5)
                                            ->get();
                                    @endphp
                                    @if ($wishlistCount > 0)
                                        <span class="cart-badge">{{ $wishlistCount }}</span>
                                    @endif
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm p-3" aria-labelledby="wishlistDropdown"
                                style="width: 300px; border-radius: 12px;">
                                <h6 class="dropdown-header px-0 mb-3 fw-bold text-dark">
                                    <i class="fas fa-heart text-danger me-2"></i>My Wishlist Preview
                                </h6>
                                @forelse($wishlistItemsPreview as $item)
                                    <li class="mb-3">
                                        <a href="{{ route('frontend.products.show', $item->product->id) }}"
                                            class="text-decoration-none dropdown-item p-0 bg-transparent">
                                            <div class="d-flex align-items-center">
                                                @php $images = $item->product->image ? explode(',', $item->product->image) : []; @endphp
                                                <img src="{{ asset('images/products/' . ($images[0] ?? 'no-image.png')) }}"
                                                    alt="{{ $item->product->name }}"
                                                    style="width: 50px; height: 50px; object-fit: contain;"
                                                    class="me-3 rounded border bg-light">
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <div class="text-truncate fw-bold text-dark small mb-1">
                                                        {{ $item->product->name }}</div>
                                                    <div class="text-primary fw-bold small">
                                                        ₹{{ number_format($item->product->price, 2) }}</div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-center py-3 text-muted">
                                        <i class="far fa-heart fa-2x mb-2 d-block opacity-25"></i>
                                        <small>Your wishlist is empty</small>
                                    </li>
                                @endforelse
                                @if ($wishlistCount > 0)
                                    <li>
                                        <hr class="dropdown-divider my-3">
                                    </li>
                                    <li>
                                        <a class="btn btn-primary btn-sm w-100 py-2"
                                            href="{{ route('frontend.wishlist') }}">
                                            View All Wishlist ({{ $wishlistCount }})
                                        </a>
                                    </li>
                                @else
                                    <li>
                                        <hr class="dropdown-divider my-3">
                                    </li>
                                    <li>
                                        <a class="btn btn-outline-primary btn-sm w-100 py-2"
                                            href="{{ route('frontend.products.index') }}">
                                            Go to Shop
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endauth
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
                                    <h6 class="dropdown-header">Account Management</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('customer.profile') ? 'active' : '' }}"
                                        href="{{ route('customer.profile') }}">
                                        <i class="fas fa-user me-2"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('frontend.my-panel') ? 'active' : '' }}"
                                        href="{{ route('frontend.my-panel') }}">
                                        <i class="fas fa-th-large me-2 text-primary"></i> My Panel
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                {{--  <li>
                                    <a class="dropdown-item" href="{{ route('frontend.orders') }}">
                                        <i class="fas fa-receipt me-2"></i> My Orders
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('frontend.wishlist') }}">
                                        <i class="fas fa-heart me-2 text-danger"></i> Wishlist
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>  --}}
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
        <div class="container py-5">
            <!-- Top Section: Logo + Sections -->
            <div class="row mb-4">
                <!-- Brand Section -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <a class="navbar-brand-with-logo mb-3 d-block" href="{{ route('frontend.home') }}">
                        @if (isset($layoutSettings) && $layoutSettings->frontend_logo_url)
                            <img src="{{ $layoutSettings->frontend_logo_url }}" alt="{{ $appName }}"
                                class="navbar-brand-logo">
                        @else
                            <span class="navbar-brand">
                                <i class="{{ $layoutSettings->frontend_icon ?? 'fas fa-store' }} me-2"></i>
                                <span class="navbar-brand-text">{{ $layoutSettings->site_title ?? $appName }}</span>
                            </span>
                        @endif
                    </a>
                    <p class="text-muted small">Your trusted online shopping destination for quality products and
                        exceptional service.</p>

                    <!-- Social Media -->
                    <div class="mt-3">
                        <h6 class="fw-bold mb-3">Follow Us</h6>
                        <div class="social-links">
                            @if (isset($layoutSettings) && !empty($layoutSettings->social_links))
                                @foreach ($layoutSettings->social_links as $social)
                                    @if (isset($social['url']) && $social['url'])
                                        <a href="{{ $social['url'] }}" target="_blank"
                                            class="text-muted text-decoration-none me-2 social-icon"
                                            title="{{ $social['title'] ?? '' }}">
                                            <i class="{{ $social['icon'] ?? 'fab fa-link' }}"></i>
                                        </a>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        @if (isset($layoutSettings) && $layoutSettings->footer_menu)
                            @foreach ($layoutSettings->footer_menu as $item)
                                <li class="mb-2">
                                    <a href="{{ $item['url'] ?? '#' }}"
                                        class="text-decoration-none text-muted hover-link">
                                        {{ $item['label'] ?? '' }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            @foreach ($dynamicPages as $page)
                                <li class="mb-2">
                                    <a href="{{ route('page.show', $page->slug) }}"
                                        class="text-decoration-none text-muted hover-link">
                                        {{ $page->title }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <!-- Contact Information -->
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <h6 class="fw-bold mb-3">Contact Us</h6>
                    <div class="contact-info">
                        @if (isset($layoutSettings))
                            @if ($layoutSettings->contact_email)
                                @foreach ($layoutSettings->contact_email as $email)
                                    @if ($email)
                                        <p class="mb-2 text-muted small d-flex align-items-start">
                                            <i class="fas fa-envelope me-2 mt-1" style="color: #667eea;"></i>
                                            <a href="mailto:{{ $email }}"
                                                class="text-decoration-none text-muted hover-link text-break">{{ $email }}</a>
                                        </p>
                                    @endif
                                @endforeach
                            @endif

                            @if ($layoutSettings->contact_phone)
                                @foreach ($layoutSettings->contact_phone as $phone)
                                    @if ($phone)
                                        <p class="mb-2 text-muted small d-flex align-items-start">
                                            <i class="fas fa-phone me-2 mt-1" style="color: #667eea;"></i>
                                            <a href="tel:{{ $phone }}"
                                                class="text-decoration-none text-muted hover-link">{{ $phone }}</a>
                                        </p>
                                    @endif
                                @endforeach
                            @endif

                            @if ($layoutSettings->contact_address)
                                <p class="mb-2 text-muted small d-flex align-items-start">
                                    <i class="fas fa-location-dot me-2 mt-1" style="color: #667eea;"></i>
                                    @if ($layoutSettings->address_link)
                                        <a href="{{ $layoutSettings->address_link }}" target="_blank"
                                            class="text-decoration-none text-muted hover-link">
                                            {{ $layoutSettings->contact_address }}
                                        </a>
                                    @else
                                        <span>{{ $layoutSettings->contact_address }}</span>
                                    @endif
                                </p>
                            @endif
                        @endif

                        {{-- Contact Modal Trigger in Footer - Moved up and outside conditional details --}}
                        <div class="mt-3">
                            <button type="button"
                                class="btn btn-primary btn-sm rounded-pill px-4 py-2 shadow-sm border-0"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
                                data-bs-toggle="modal" data-bs-target="#contactModal">
                                <i class="fas fa-comment-dots fa-lg me-2"></i>
                                <span class="fw-bold">Contact Us</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Google Map -->
                <div class="col-lg-4 col-md-6">
                    @if ($layoutSettings->map_html)
                        <h6 class="fw-bold mb-3">Find Us</h6>
                        <div class="map-container overflow-hidden rounded shadow-sm" style="height: 200px;">
                            {!! $layoutSettings->map_html !!}
                        </div>
                        <style>
                            .map-container iframe {
                                width: 100% !important;
                                height: 200px !important;
                                border: 0 !important;
                            }
                        </style>
                    @endif
                </div>
            </div>

            <!-- Bottom Section: Copyright -->
            <div class="row pt-4 border-top">
                <div class="col-12 text-center">
                    @if (isset($layoutSettings) && $layoutSettings->footer_text)
                        <p class="text-muted mb-0 small">{{ $layoutSettings->footer_text }}</p>
                    @else
                        <p class="text-muted mb-0 small">© {{ date('Y') }}
                            {{ $appName ?? config('app.name', 'MyShop') }}. All rights
                            reserved.</p>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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

    {{-- Global Alert Handler --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error') }}"
                });
            @endif

            @if (session('info'))
                Swal.fire({
                    icon: 'info',
                    text: "{{ session('info') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            @if (session('warning'))
                Swal.fire({
                    icon: 'warning',
                    text: "{{ session('warning') }}"
                });
            @endif
        });
    </script>

    <!-- Contact Us Floating Button -->
    <div class="position-fixed bottom-0 end-0 m-4" style="z-index: 9999;">
        <button type="button" class="btn btn-primary rounded-pill shadow-lg p-3 d-flex align-items-center border-0"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);" data-bs-toggle="modal"
            data-bs-target="#contactModal">
            <i class="fas fa-comment-dots fa-lg me-2"></i>
            <span class="fw-bold">Contact Us</span>
        </button>
    </div>

    <!-- Contact Us Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold" id="contactModalLabel">
                        <i class="fas fa-envelope me-2"></i> Send us a Message
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="contactForm" action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="name" class="form-label small fw-bold text-muted">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                placeholder="Enter your name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold text-muted">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required
                                placeholder="name@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="number" class="form-label small fw-bold text-muted">Phone Number</label>
                            <input type="text" class="form-control" id="number" name="number" required
                                placeholder="Your phone number">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label small fw-bold text-muted">Your Message</label>
                            <textarea class="form-control" id="message" name="message" rows="4" required
                                placeholder="How can we help you?"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4 py-2" id="submitContactBtn">
                            <span class="spinner-border spinner-border-sm d-none me-2" role="status"
                                aria-hidden="true"></span>
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Contact Form Ajax Handling --}}
    <script>
        $(document).ready(function() {
            $('#contactForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const btn = $('#submitContactBtn');
                const spinner = btn.find('.spinner-border');

                // Disable button and show spinner
                btn.prop('disabled', true);
                spinner.removeClass('d-none');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('contactModal'))
                            .hide();

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Message Sent!',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false
                        });

                        // Reset form
                        form[0].reset();
                    },
                    error: function(xhr) {
                        let msg = 'Something went wrong. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            // Show first validation error
                            msg = Object.values(xhr.responseJSON.errors)[0][0];
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: msg
                        });
                    },
                    complete: function() {
                        // Re-enable button and hide spinner
                        btn.prop('disabled', false);
                        spinner.addClass('d-none');
                    }
                });
            });
        });
    </script>

    @stack('scripts')

</body>

</html>
