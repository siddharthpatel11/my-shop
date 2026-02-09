@extends('layouts.frontend.app')

@section('title', 'Create Account')

@section('content')
    <div class="min-vh-100 d-flex align-items-center bg-light py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">

                    {{-- Register Card --}}
                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-body p-5">

                            {{-- Logo/Header --}}
                            <div class="text-center mb-4">
                                <div class="bg-gradient-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-user-plus fa-2x text-white"></i>
                                </div>
                                <h3 class="fw-bold mb-1">Create Account</h3>
                                <p class="text-muted">Join us today!</p>
                            </div>

                            {{-- Error Messages --}}
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            {{-- Registration Form --}}
                            <form method="POST" action="{{ route('customer.register.post') }}">
                                @csrf

                                {{-- Name --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-user text-primary me-2"></i>Full Name
                                    </label>
                                    <input type="text"
                                           name="name"
                                           value="{{ old('name') }}"
                                           class="form-control form-control-lg"
                                           placeholder="Enter your full name"
                                           required>
                                </div>

                                {{-- Email --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-envelope text-primary me-2"></i>Email Address
                                    </label>
                                    <input type="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           class="form-control form-control-lg"
                                           placeholder="Enter your email"
                                           required>
                                </div>

                                {{-- Phone Number --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-phone text-primary me-2"></i>Phone Number
                                    </label>
                                    <input type="text"
                                           name="phone_number"
                                           value="{{ old('phone_number') }}"
                                           class="form-control form-control-lg"
                                           placeholder="Enter your phone number"
                                           required>
                                </div>

                                {{-- Password --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-lock text-primary me-2"></i>Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                               name="password"
                                               id="password"
                                               class="form-control form-control-lg"
                                               placeholder="Create a password"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                            <i class="fas fa-eye" id="toggleIcon1"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Minimum 6 characters</small>
                                </div>

                                {{-- Confirm Password --}}
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-lock text-primary me-2"></i>Confirm Password
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                               name="password_confirmation"
                                               id="password_confirmation"
                                               class="form-control form-control-lg"
                                               placeholder="Re-enter your password"
                                               required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                            <i class="fas fa-eye" id="toggleIcon2"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Terms & Conditions --}}
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label small" for="terms">
                                        I agree to the <a href="#" class="text-primary">Terms & Conditions</a>
                                    </label>
                                </div>

                                {{-- Register Button --}}
                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </button>

                            </form>

                            {{-- Divider --}}
                            <div class="text-center my-4">
                                <span class="text-muted">Already have an account?</span>
                            </div>

                            {{-- Login Link --}}
                            <a href="{{ route('customer.login') }}" class="btn btn-outline-primary btn-lg w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
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

        .alert ul {
            padding-left: 20px;
        }
    </style>

    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const iconId = fieldId === 'password' ? 'toggleIcon1' : 'toggleIcon2';
            const toggleIcon = document.getElementById(iconId);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
@endsection
