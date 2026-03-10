@extends('layouts.frontend.app')

@section('title', 'My Profile')

@section('content')
    <div class="bg-gradient-primary py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 text-center">
                    <h1 class="display-5 fw-bold text-white mb-2">My Profile</h1>
                    <p class="text-white-50">Manage your account information</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row g-4">

                    {{-- Profile Card --}}
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body text-center p-4">
                                <div class="bg-gradient-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 100px; height: 100px;">
                                    <i class="fas fa-user fa-3x text-white"></i>
                                </div>
                                <h4 class="fw-bold mb-1">{{ $customer->name }}</h4>
                                <p class="text-muted small mb-3">
                                    <i class="fas fa-envelope me-2"></i>{{ $customer->email }}
                                </p>
                                <span class="badge bg-success-subtle text-success px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Details Card --}}
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-header bg-white border-0 p-4">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-id-card text-primary me-2"></i>Account Information
                                </h5>
                            </div>
                            <div class="card-body p-4">

                                {{-- Full Name --}}
                                <div class="mb-4 pb-3 border-bottom">
                                    <div class="row">
                                        <div class="col-5">
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-user text-primary me-2"></i>Full Name
                                            </p>
                                        </div>
                                        <div class="col-7">
                                            <p class="fw-semibold mb-0">{{ $customer->name }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="mb-4 pb-3 border-bottom">
                                    <div class="row">
                                        <div class="col-5">
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-envelope text-primary me-2"></i>Email Address
                                            </p>
                                        </div>
                                        <div class="col-7 d-flex justify-content-between align-items-center">
                                            <p class="fw-semibold mb-0" id="current-email">{{ $customer->email }}</p>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal" data-bs-target="#editEmailModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Phone --}}
                                <div class="mb-4 pb-3 border-bottom">
                                    <div class="row">
                                        <div class="col-5">
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-phone text-primary me-2"></i>Phone Number
                                            </p>
                                        </div>
                                        <div class="col-7">
                                            <p class="fw-semibold mb-0">{{ $customer->phone_number }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="mb-4 pb-3 border-bottom">
                                    <div class="row">
                                        <div class="col-5">
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-shield-alt text-primary me-2"></i>Account Status
                                            </p>
                                        </div>
                                        <div class="col-7">
                                            <span class="badge bg-success px-3 py-2">
                                                {{ ucfirst($customer->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Member Since --}}
                                <div class="mb-0">
                                    <div class="row">
                                        <div class="col-5">
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-calendar-alt text-primary me-2"></i>Member Since
                                            </p>
                                        </div>
                                        <div class="col-7">
                                            <p class="fw-semibold mb-0">
                                                {{ $customer->created_at ? $customer->created_at->format('M d, Y') : 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                {{-- Action Buttons --}}
                <div class="row g-3 mt-4">
                    <div class="col-md-6">
                        <a href="{{ route('frontend.products.index') }}" class="btn btn-outline-primary btn-lg w-100">
                            <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                        </a>
                    </div>
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('customer.logout') }}" class="d-inline w-100">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-lg w-100">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="row g-3 mt-4">
                    <div class="col-md-4">
                        <div class="card border-0 bg-primary bg-opacity-10 text-center p-4">
                            <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                            <h3 class="fw-bold mb-0" id="cartItemsCount">0</h3>
                            <small class="text-muted">Items in Cart</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-success bg-opacity-10 text-center p-4">
                            <i class="fas fa-box fa-2x text-success mb-2"></i>
                            <h3 class="fw-bold mb-0">0</h3>
                            <small class="text-muted">Orders Placed</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-warning bg-opacity-10 text-center p-4">
                            <i class="fas fa-heart fa-2x text-warning mb-2"></i>
                            <h3 class="fw-bold mb-0">0</h3>
                            <small class="text-muted">Wishlist Items</small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Edit Email Modal -->
    <div class="modal fade" id="editEmailModal" tabindex="-1" aria-labelledby="editEmailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="editEmailModalLabel">Change Email Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Step 1: Send OTP -->
                    <div id="step-send-otp">
                        <p class="text-muted mb-4">Click below to send a verification code to your current email:
                            <strong>{{ $customer->email }}</strong>
                        </p>
                        <button type="button" class="btn btn-primary w-100 py-2 rounded-3" id="btn-send-otp">
                            <span class="spinner-border spinner-border-sm d-none" id="spinner-send"
                                role="status"></span>
                            Send Verification Code
                        </button>
                    </div>

                    <!-- Step 2: Verify OTP -->
                    <div id="step-verify-otp" class="d-none">
                        <p class="text-muted mb-4">Enter the 6-digit code sent to your email.</p>
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg text-center letter-spacing-5"
                                id="otp-input" maxlength="6" placeholder="000000">
                            <div class="invalid-feedback" id="otp-error"></div>
                        </div>
                        <button type="button" class="btn btn-primary w-100 py-2 rounded-3" id="btn-verify-otp">
                            <span class="spinner-border spinner-border-sm d-none" id="spinner-verify"
                                role="status"></span>
                            Verify Code
                        </button>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-link btn-sm text-decoration-none"
                                id="btn-resend-otp">Resend Code</button>
                        </div>
                    </div>

                    <!-- Step 3: Enter New Email -->
                    <div id="step-new-email" class="d-none">
                        <p class="text-muted mb-4">Verification successful! Enter your new email address.</p>
                        <div class="mb-3">
                            <label for="new_email" class="form-label">New Email Address</label>
                            <input type="email" class="form-control" id="new_email" placeholder="name@example.com">
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>
                        <button type="button" class="btn btn-success w-100 py-2 rounded-3" id="btn-update-email">
                            <span class="spinner-border spinner-border-sm d-none" id="spinner-update"
                                role="status"></span>
                            Update Email
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .letter-spacing-5 {
            letter-spacing: 5px;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card {
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update cart count on page load
            const cart = JSON.parse(localStorage.getItem('shoppingCart') || '[]');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            const cartCounter = document.getElementById('cartItemsCount');
            if (cartCounter) cartCounter.textContent = totalItems;

            // Email Change Logic
            const btnSendOtp = document.getElementById('btn-send-otp');
            const btnVerifyOtp = document.getElementById('btn-verify-otp');
            const btnResendOtp = document.getElementById('btn-resend-otp');
            const btnUpdateEmail = document.getElementById('btn-update-email');

            const step1 = document.getElementById('step-send-otp');
            const step2 = document.getElementById('step-verify-otp');
            const step3 = document.getElementById('step-new-email');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send OTP
            btnSendOtp.addEventListener('click', function() {
                sendOtp();
            });

            btnResendOtp.addEventListener('click', function() {
                sendOtp();
            });

            function sendOtp() {
                btnSendOtp.disabled = true;
                btnResendOtp.disabled = true;
                document.getElementById('spinner-send').classList.remove('d-none');

                fetch('{{ route('customer.email-change.send-otp') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            step1.classList.add('d-none');
                            step2.classList.remove('d-none');
                        } else {
                            alert(data.message || 'Error sending OTP');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    })
                    .finally(() => {
                        btnSendOtp.disabled = false;
                        btnResendOtp.disabled = false;
                        document.getElementById('spinner-send').classList.add('d-none');
                    });
            }

            // Verify OTP
            btnVerifyOtp.addEventListener('click', function() {
                const otp = document.getElementById('otp-input').value;
                if (!otp || otp.length !== 6) {
                    document.getElementById('otp-input').classList.add('is-invalid');
                    document.getElementById('otp-error').textContent = 'Please enter a 6-digit code.';
                    return;
                }

                btnVerifyOtp.disabled = true;
                document.getElementById('spinner-verify').classList.remove('d-none');
                document.getElementById('otp-input').classList.remove('is-invalid');

                fetch('{{ route('customer.email-change.verify-otp') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            otp: otp
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            step2.classList.add('d-none');
                            step3.classList.remove('d-none');
                        } else {
                            document.getElementById('otp-input').classList.add('is-invalid');
                            document.getElementById('otp-error').textContent = data.message ||
                                'Invalid OTP';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    })
                    .finally(() => {
                        btnVerifyOtp.disabled = false;
                        document.getElementById('spinner-verify').classList.add('d-none');
                    });
            });

            // Update Email
            btnUpdateEmail.addEventListener('click', function() {
                const newEmail = document.getElementById('new_email').value;
                if (!newEmail) {
                    document.getElementById('new_email').classList.add('is-invalid');
                    document.getElementById('email-error').textContent =
                        'Please enter a new email address.';
                    return;
                }

                btnUpdateEmail.disabled = true;
                document.getElementById('spinner-update').classList.remove('d-none');
                document.getElementById('new_email').classList.remove('is-invalid');

                fetch('{{ route('customer.email-change.update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            new_email: newEmail
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            document.getElementById('new_email').classList.add('is-invalid');
                            document.getElementById('email-error').textContent = data.message ||
                                'Error updating email';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred. Please try again.');
                    })
                    .finally(() => {
                        btnUpdateEmail.disabled = false;
                        document.getElementById('spinner-update').classList.add('d-none');
                    });
            });
        });
    </script>
@endsection
