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
                                        <div class="col-7 d-flex justify-content-between align-items-center">
                                            <p class="fw-semibold mb-0" id="current-phone">{{ $customer->phone_number }}</p>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal" data-bs-target="#editPhoneModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
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
                            <h3 class="fw-bold mb-0" id="cartItemsCount">{{ $cartCount }}</h3>
                            <small class="text-muted">Items in Cart</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-success bg-opacity-10 text-center p-4">
                            <i class="fas fa-box fa-2x text-success mb-2"></i>
                            <h3 class="fw-bold mb-0">{{ $orderCount }}</h3>
                            <small class="text-muted">Orders Placed</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 bg-warning bg-opacity-10 text-center p-4">
                            <i class="fas fa-heart fa-2x text-warning mb-2"></i>
                            <h3 class="fw-bold mb-0">{{ $wishlistCount }}</h3>
                            <small class="text-muted">Wishlist Items</small>
                        </div>
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
                        <p class="text-muted mb-4">Enter the 6-digit code sent to your email address.</p>
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
                            <input type="email" class="form-control" id="new_email"
                                placeholder="new-email@example.com">
                            <div class="invalid-feedback" id="email-error"></div>
                        </div>
                        <button type="button" class="btn btn-success w-100 py-2 rounded-3" id="btn-update-email">
                            <span class="spinner-border spinner-border-sm d-none" id="spinner-update"
                                role="status"></span>
                            Update Email Address
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Phone Modal -->
    <div class="modal fade" id="editPhoneModal" tabindex="-1" aria-labelledby="editPhoneModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="editPhoneModalLabel">Change Phone Number</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Step 1: Send OTP -->
                    <div id="phone-step-send-otp">
                        <p class="text-muted mb-4">Click below to send a verification code to your current phone number:
                            <strong>{{ $customer->phone_number }}</strong>
                        </p>
                        <button type="button" class="btn btn-primary w-100 py-2 rounded-3" id="btn-send-phone-otp">
                            <span class="spinner-border spinner-border-sm d-none" id="phone-spinner-send"
                                role="status"></span>
                            Send Verification Code
                        </button>
                    </div>

                    <!-- Step 2: Verify OTP -->
                    <div id="phone-step-verify-otp" class="d-none">
                        <p class="text-muted mb-4">Enter the 6-digit code sent to your phone number.</p>
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg text-center letter-spacing-5"
                                id="phone-otp-input" maxlength="6" placeholder="000000">
                            <div class="invalid-feedback" id="phone-otp-error"></div>
                        </div>
                        <button type="button" class="btn btn-primary w-100 py-2 rounded-3" id="btn-verify-phone-otp">
                            <span class="spinner-border spinner-border-sm d-none" id="phone-spinner-verify"
                                role="status"></span>
                            Verify Code
                        </button>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-link btn-sm text-decoration-none"
                                id="btn-resend-phone-otp">Resend Code</button>
                        </div>
                    </div>

                    <!-- Step 3: Enter New Phone -->
                    <div id="phone-step-new-phone" class="d-none">
                        <p class="text-muted mb-4">Verification successful! Enter your new phone number.</p>
                        <div class="mb-3">
                            <label for="new_phone" class="form-label">New Phone Number</label>
                            <input type="text" class="form-control" id="new_phone" placeholder="1234567890">
                            <div class="invalid-feedback" id="phone-error"></div>
                        </div>
                        <button type="button" class="btn btn-success w-100 py-2 rounded-3" id="btn-update-phone">
                            <span class="spinner-border spinner-border-sm d-none" id="phone-spinner-update"
                                role="status"></span>
                            Update Phone Number
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
            // Email Change Logic
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const btnSendOtp = document.getElementById('btn-send-otp');
            const btnVerifyOtp = document.getElementById('btn-verify-otp');
            const btnResendOtp = document.getElementById('btn-resend-otp');
            const btnUpdateEmail = document.getElementById('btn-update-email');

            const step1 = document.getElementById('step-send-otp');
            const step2 = document.getElementById('step-verify-otp');
            const step3 = document.getElementById('step-new-email');

            if (btnSendOtp) {
                btnSendOtp.addEventListener('click', () => sendOtp());
            }
            if (btnResendOtp) {
                btnResendOtp.addEventListener('click', () => sendOtp());
            }

            function sendOtp() {
                if (!btnSendOtp) return;
                btnSendOtp.disabled = true;
                if (btnResendOtp) btnResendOtp.disabled = true;
                const spinner = document.getElementById('spinner-send');
                if (spinner) spinner.classList.remove('d-none');

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
                            if (step1) step1.classList.add('d-none');
                            if (step2) step2.classList.remove('d-none');

                            if (data.otp_debug) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Verification Code',
                                    text: 'Your OTP is: ' + data.otp_debug,
                                    footer: 'Note: This OTP is shown for testing.'
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Error sending OTP'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.'
                        });
                    })
                    .finally(() => {
                        btnSendOtp.disabled = false;
                        if (btnResendOtp) btnResendOtp.disabled = false;
                        if (spinner) spinner.classList.add('d-none');
                    });
            }

            if (btnVerifyOtp) {
                btnVerifyOtp.addEventListener('click', function() {
                    const otpInput = document.getElementById('otp-input');
                    const otp = otpInput ? otpInput.value : '';
                    if (!otp || otp.length !== 6) {
                        if (otpInput) otpInput.classList.add('is-invalid');
                        const errorDiv = document.getElementById('otp-error');
                        if (errorDiv) errorDiv.textContent = 'Please enter a 6-digit code.';
                        return;
                    }

                    btnVerifyOtp.disabled = true;
                    const spinner = document.getElementById('spinner-verify');
                    if (spinner) spinner.classList.remove('d-none');
                    if (otpInput) otpInput.classList.remove('is-invalid');

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
                                if (step2) step2.classList.add('d-none');
                                if (step3) step3.classList.remove('d-none');
                            } else {
                                if (otpInput) otpInput.classList.add('is-invalid');
                                const errorDiv = document.getElementById('otp-error');
                                if (errorDiv) errorDiv.textContent = data.message || 'Invalid OTP';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred. Please try again.'
                            });
                        })
                        .finally(() => {
                            btnVerifyOtp.disabled = false;
                            if (spinner) spinner.classList.add('d-none');
                        });
                });
            }

            if (btnUpdateEmail) {
                btnUpdateEmail.addEventListener('click', function() {
                    const emailInput = document.getElementById('new_email');
                    const newEmail = emailInput ? emailInput.value : '';
                    if (!newEmail) {
                        if (emailInput) emailInput.classList.add('is-invalid');
                        const errorDiv = document.getElementById('email-error');
                        if (errorDiv) errorDiv.textContent = 'Please enter a new email address.';
                        return;
                    }

                    btnUpdateEmail.disabled = true;
                    const spinner = document.getElementById('spinner-update');
                    if (spinner) spinner.classList.remove('d-none');
                    if (emailInput) emailInput.classList.remove('is-invalid');

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
                                if (emailInput) emailInput.classList.add('is-invalid');
                                const errorDiv = document.getElementById('email-error');
                                if (errorDiv) errorDiv.textContent = data.message ||
                                    'Error updating email';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred. Please try again.'
                            });
                        })
                        .finally(() => {
                            btnUpdateEmail.disabled = false;
                            if (spinner) spinner.classList.add('d-none');
                        });
                });
            }

            // Phone Change Logic
            const btnSendPhoneOtp = document.getElementById('btn-send-phone-otp');
            const btnVerifyPhoneOtp = document.getElementById('btn-verify-phone-otp');
            const btnResendPhoneOtp = document.getElementById('btn-resend-phone-otp');
            const btnUpdatePhone = document.getElementById('btn-update-phone');

            const phoneStep1 = document.getElementById('phone-step-send-otp');
            const phoneStep2 = document.getElementById('phone-step-verify-otp');
            const phoneStep3 = document.getElementById('phone-step-new-phone');

            if (btnSendPhoneOtp) {
                btnSendPhoneOtp.addEventListener('click', () => sendPhoneOtp());
            }
            if (btnResendPhoneOtp) {
                btnResendPhoneOtp.addEventListener('click', () => sendPhoneOtp());
            }

            function sendPhoneOtp() {
                if (!btnSendPhoneOtp) return;
                btnSendPhoneOtp.disabled = true;
                if (btnResendPhoneOtp) btnResendPhoneOtp.disabled = true;
                const spinner = document.getElementById('phone-spinner-send');
                if (spinner) spinner.classList.remove('d-none');

                fetch('{{ route('customer.phone-change.send-otp') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (phoneStep1) phoneStep1.classList.add('d-none');
                            if (phoneStep2) phoneStep2.classList.remove('d-none');

                            Swal.fire({
                                icon: 'success',
                                title: 'OTP Sent',
                                text: data.message,
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'Error sending OTP'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.'
                        });
                    })
                    .finally(() => {
                        btnSendPhoneOtp.disabled = false;
                        if (btnResendPhoneOtp) btnResendPhoneOtp.disabled = false;
                        if (spinner) spinner.classList.add('d-none');
                    });
            }

            if (btnVerifyPhoneOtp) {
                btnVerifyPhoneOtp.addEventListener('click', function() {
                    const otpInput = document.getElementById('phone-otp-input');
                    const otp = otpInput ? otpInput.value : '';
                    if (!otp || otp.length !== 6) {
                        if (otpInput) otpInput.classList.add('is-invalid');
                        const errorDiv = document.getElementById('phone-otp-error');
                        if (errorDiv) errorDiv.textContent = 'Please enter a 6-digit code.';
                        return;
                    }

                    btnVerifyPhoneOtp.disabled = true;
                    const spinner = document.getElementById('phone-spinner-verify');
                    if (spinner) spinner.classList.remove('d-none');
                    if (otpInput) otpInput.classList.remove('is-invalid');

                    fetch('{{ route('customer.phone-change.verify-otp') }}', {
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
                                if (phoneStep2) phoneStep2.classList.add('d-none');
                                if (phoneStep3) phoneStep3.classList.remove('d-none');
                            } else {
                                if (otpInput) otpInput.classList.add('is-invalid');
                                const errorDiv = document.getElementById('phone-otp-error');
                                if (errorDiv) errorDiv.textContent = data.message || 'Invalid OTP';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred. Please try again.'
                            });
                        })
                        .finally(() => {
                            btnVerifyPhoneOtp.disabled = false;
                            if (spinner) spinner.classList.add('d-none');
                        });
                });
            }

            if (btnUpdatePhone) {
                btnUpdatePhone.addEventListener('click', function() {
                    const phoneInput = document.getElementById('new_phone');
                    const newPhone = phoneInput ? phoneInput.value : '';
                    if (!newPhone) {
                        if (phoneInput) phoneInput.classList.add('is-invalid');
                        const errorDiv = document.getElementById('phone-error');
                        if (errorDiv) errorDiv.textContent = 'Please enter a new phone number.';
                        return;
                    }

                    btnUpdatePhone.disabled = true;
                    const spinner = document.getElementById('phone-spinner-update');
                    if (spinner) spinner.classList.remove('d-none');
                    if (phoneInput) phoneInput.classList.remove('is-invalid');

                    fetch('{{ route('customer.phone-change.update') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                new_phone: newPhone
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                if (phoneInput) phoneInput.classList.add('is-invalid');
                                const errorDiv = document.getElementById('phone-error');
                                if (errorDiv) errorDiv.textContent = data.message ||
                                    'Error updating phone number';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred. Please try again.'
                            });
                        })
                        .finally(() => {
                            btnUpdatePhone.disabled = false;
                            if (spinner) spinner.classList.add('d-none');
                        });
                });
            }
        });
    </script>
@endsection
