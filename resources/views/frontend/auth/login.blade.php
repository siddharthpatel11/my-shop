@extends('layouts.frontend.app')

@section('title', 'Customer Login')

@section('content')
    <div class="min-vh-100 d-flex align-items-center bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">

                    {{-- Login Card --}}
                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-body p-5">

                            {{-- Logo/Header --}}
                            <div class="text-center mb-4">
                                <div class="bg-gradient-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 80px; height: 80px;">
                                    <i class="fas fa-user fa-2x text-white"></i>
                                </div>
                                <h3 class="fw-bold mb-1">Welcome Back!</h3>
                                <p class="text-muted">Login to your account</p>
                            </div>

                            {{-- Success Message --}}
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- Error Messages --}}
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- Login Form --}}
                            <form method="POST" action="{{ route('customer.login.post') }}">
                                @csrf

                                {{-- Email --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-envelope text-primary me-2"></i>Email Address
                                    </label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        class="form-control form-control-lg" placeholder="Enter your email" required>
                                </div>

                                {{-- Password --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-lock text-primary me-2"></i>Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="password" id="password"
                                            class="form-control form-control-lg" placeholder="Enter your password" required>
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="fas fa-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Remember Me --}}
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>

                                {{-- Login Button --}}
                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>

                            </form>

                            {{-- Divider --}}
                            <div class="social-divider">
                                <span>or continue with</span>
                            </div>

                            {{-- Social Login Buttons --}}
                            <div class="social-buttons-row">

                                {{-- Google --}}
                                <a href="{{ route('customer.social.redirect', 'google') }}"
                                    class="social-btn social-btn--google" title="Sign in with Google">
                                    <span class="social-btn__icon">
                                        <svg width="20" height="20" viewBox="0 0 48 48"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill="#EA4335"
                                                d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                                            <path fill="#4285F4"
                                                d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                                            <path fill="#FBBC05"
                                                d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                                            <path fill="#34A853"
                                                d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                                        </svg>
                                    </span>
                                    <span class="social-btn__label"></span>
                                    <span class="social-btn__ripple"></span>
                                </a>

                                {{-- Facebook --}}
                                <a href="{{ route('customer.social.redirect', 'facebook') }}"
                                    class="social-btn social-btn--facebook" title="Sign in with Facebook">
                                    <span class="social-btn__icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg" fill="#fff">
                                            <path
                                                d="M24 12.073C24 5.446 18.627 0 12 0S0 5.446 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.269h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z" />
                                        </svg>
                                    </span>
                                    <span class="social-btn__label"></span>
                                    <span class="social-btn__ripple"></span>
                                </a>

                                {{-- Twitter / X --}}
                                <a href="{{ route('customer.social.redirect', 'twitter') }}"
                                    class="social-btn social-btn--twitter" title="Sign in with X (Twitter)">
                                    <span class="social-btn__icon">
                                        <svg width="18" height="18" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg" fill="#fff">
                                            <path
                                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.747l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                        </svg>
                                    </span>
                                    <span class="social-btn__label"></span>
                                    <span class="social-btn__ripple"></span>
                                </a>

                            </div>

                            {{-- Register Link --}}
                            <div class="text-center my-4">
                                <span class="text-muted">Don't have an account?</span>
                            </div>

                            {{-- Register Link --}}
                            <a href="{{ route('customer.register') }}" class="btn btn-outline-primary btn-lg w-100">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </a>

                        </div>
                    </div>

                    {{-- Back to Home --}}
                    <div class="text-center mt-4">
                        <a href="{{ route('frontend.home') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-2"></i>Back to Home
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card {
            transition: transform 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #653a8b 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .social-divider {
            position: relative;
            text-align: center;
            margin: 1.5rem 0 1.25rem;
            color: #9ca3af;
            font-size: 0.78rem;
            letter-spacing: 0.04em;
            text-transform: lowercase;
        }

        .social-divider::before,
        .social-divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: calc(50% - 70px);
            height: 1px;
            background: #e5e7eb;
        }

        .social-divider::before {
            left: 0;
        }

        .social-divider::after {
            right: 0;
        }

        /* ---------- Row ---------- */
        .social-buttons-row {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 1.25rem;
        }

        /* ---------- Base Button ---------- */
        .social-btn {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            flex: 1;
            /* equal width */
            max-width: 140px;
            padding: 10px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            text-decoration: none;
            cursor: pointer;
            border: none;
            outline: none;
            transition:
                transform 0.18s cubic-bezier(.34, 1.56, .64, 1),
                box-shadow 0.18s ease,
                filter 0.18s ease;
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        .social-btn:focus-visible {
            outline: 3px solid #667eea;
            outline-offset: 2px;
        }

        .social-btn:hover {
            transform: translateY(-3px) scale(1.03);
        }

        .social-btn:active {
            transform: translateY(0) scale(0.97);
            transition-duration: 0.08s;
        }

        /* ---------- Icon ---------- */
        .social-btn__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            line-height: 1;
        }

        /* ---------- Label ---------- */
        .social-btn__label {
            white-space: nowrap;
            line-height: 1;
        }

        /* ---------- Google ---------- */
        .social-btn--google {
            background: #ffffff;
            color: #374151;
            border: 1.5px solid #e5e7eb;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        }

        .social-btn--google:hover {
            color: #1f2937;
            box-shadow: 0 6px 20px rgba(66, 133, 244, 0.2);
            border-color: #c7d7fc;
        }

        /* ---------- Facebook ---------- */
        .social-btn--facebook {
            background: #1877F2;
            color: #fff;
            box-shadow: 0 1px 4px rgba(24, 119, 242, 0.25);
        }

        .social-btn--facebook:hover {
            color: #fff;
            background: #1464d8;
            box-shadow: 0 6px 20px rgba(24, 119, 242, 0.4);
        }

        /* ---------- Twitter / X ---------- */
        .social-btn--twitter {
            background: #0f0f0f;
            color: #fff;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.18);
        }

        .social-btn--twitter:hover {
            color: #fff;
            background: #1a1a1a;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.28);
        }

        /* ---------- Ripple effect on click ---------- */
        .social-btn__ripple {
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.35) 0%, transparent 70%);
            transform: scale(0);
            opacity: 0;
            transition: transform 0s, opacity 0s;
        }

        .social-btn:active .social-btn__ripple {
            transform: scale(2.5);
            opacity: 1;
            transition: transform 0.35s ease-out, opacity 0.35s ease-out;
        }

        /* ---------- Loading state (optional JS) ---------- */
        .social-btn.is-loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .social-btn.is-loading .social-btn__icon svg {
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ---------- Responsive: stack on very small screens ---------- */
        @media (max-width: 360px) {
            .social-buttons-row {
                flex-direction: column;
                align-items: stretch;
            }

            .social-btn {
                max-width: 100%;
                padding: 12px 16px;
                font-size: 0.875rem;
            }
        }
    </style>
    <script>
        document.querySelectorAll('.social-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                // Prevent double-click
                btn.classList.add('is-loading');
                // Optional: swap icon to a spinner SVG while redirecting
                btn.querySelector('.social-btn__icon').innerHTML =
                    '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">' +
                    '<path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>' +
                    '</svg>';
            });
        });
    </script>

@endsection
