<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h2 class="h4 fw-bold text-dark mb-1">
                    <i class="fas fa-user-circle me-2" style="color: #6366f1;"></i>{{ __('Admin Profile') }}
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none"
                                style="color: #6366f1;">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Account Settings</li>
                    </ol>
                </nav>
            </div>
            <div>
                <span class="badge px-3 py-2 rounded-pill shadow-sm"
                    style="background: linear-gradient(135deg, #6366f1, #a855f7); color: white;">
                    <i class="fas fa-shield-alt me-1"></i> Admin Account
                </span>
            </div>
        </div>
    </x-slot>

    <div class="container-fluid py-5" style="background: #f8fafc; min-height: calc(100vh - 120px);">
        <div class="row justify-content-center">

            <div class="col-lg-11 col-xl-10">

                <!-- Profile Header Card -->
                <div
                    class="card border-0 rounded-4 overflow-hidden mb-5 custom-shadow profile-hero animate__animated animate__fadeIn">
                    <div class="card-body p-0 position-relative">
                        <div class="profile-cover"
                            style="height: 140px; background: linear-gradient(135deg, #3b82f6, #8b5cf6);"></div>
                        <div class="d-flex px-4 pb-4 position-relative profile-info-container">
                            <div
                                class="profile-avatar shadow-lg bg-white p-2 rounded-circle d-flex align-items-center justify-content-center">
                                <span class="fs-1 fw-bold"
                                    style="color: #6366f1;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            </div>
                            <div class="ms-4 mt-2">
                                <h3 class="fw-bold mb-0 text-dark">{{ auth()->user()->name }}</h3>
                                <p class="text-muted mb-0"><i
                                        class="fas fa-envelope me-2"></i>{{ auth()->user()->email }}</p>
                            </div>
                            <div class="ms-auto mt-2 text-end d-none d-sm-block">
                                <p class="text-muted small mb-1">Account Status</p>
                                <span
                                    class="badge bg-success-subtle text-success border border-success border-opacity-25 px-3 py-2 rounded-pill"><i
                                        class="fas fa-check-circle me-1"></i> Active</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2FA Status Banner -->
                <div
                    class="card border-0 rounded-4 overflow-hidden mb-5 custom-shadow animate__animated animate__fadeInUp">
                    <div class="card-body p-4 border-start border-4 {{ auth()->user()->google2fa_enabled ? 'border-primary' : 'border-warning' }}"
                        style="background: #ffffff;">
                        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between">
                            <div class="d-flex align-items-center mb-3 mb-md-0">
                                <div
                                    class="bg-{{ auth()->user()->google2fa_enabled ? 'primary' : 'warning' }}-subtle p-3 rounded-circle me-4 text-{{ auth()->user()->google2fa_enabled ? 'primary' : 'warning' }}">
                                    <i class="fas fa-shield-halved fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1 text-dark">Two-Factor Authentication</h5>
                                    <p class="text-muted small mb-0">
                                        @if(auth()->user()->google2fa_enabled)
                                            <span class="text-primary fw-bold">Enabled.</span> Your account is highly
                                            secure.
                                        @else
                                            <span class="text-warning fw-bold">Attention!</span> Secure your account by
                                            enabling 2FA.
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <a href="{{ route('admin.2fa.setup') }}"
                                class="btn {{ auth()->user()->google2fa_enabled ? 'btn-outline-primary' : 'btn-warning text-dark' }} fw-bold px-4 rounded-pill shadow-sm premium-hover">
                                <i
                                    class="fas fa-{{ auth()->user()->google2fa_enabled ? 'cog' : 'lock' }} me-2"></i>{{ auth()->user()->google2fa_enabled ? 'Manage 2FA' : 'Enable 2FA' }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row g-5">

                    <div class="col-lg-6">
                        <!-- Profile Information -->
                        <div class="card border-0 custom-shadow rounded-4 h-100 animate__animated animate__fadeInUp animate__delay-1s"
                            style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                            <div class="card-body p-4 p-md-5">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <!-- Update Password -->
                        <div class="card border-0 custom-shadow rounded-4 h-100 animate__animated animate__fadeInUp animate__delay-1s"
                            style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);">
                            <div class="card-body p-4 p-md-5">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>

                    <!-- Delete Account -->
                    <div class="col-12 mt-5">
                        <div class="card border-0 custom-shadow border-top border-danger border-4 rounded-4 animate__animated animate__fadeInUp animate__delay-2s"
                            style="background: #ffffff;">
                            <div class="card-body p-4 p-md-5">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
        <style>
            .custom-shadow {
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            }

            .profile-hero {
                margin-top: 20px;
            }

            .profile-avatar {
                width: 90px;
                height: 90px;
                margin-top: -45px;
                z-index: 10;
                cursor: pointer;
                transition: transform 0.3s ease;
            }

            .profile-avatar:hover {
                transform: scale(1.05);
            }

            .premium-hover {
                transition: all 0.3s ease;
            }

            .premium-hover:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
            }

            .animate__delay-1s {
                animation-delay: 0.2s;
            }

            .animate__delay-2s {
                animation-delay: 0.4s;
            }

            .form-control-custom {
                border: 1px solid #e2e8f0;
                background-color: #f8fafc;
                border-radius: 0.75rem;
                padding: 0.75rem 1rem;
                transition: all 0.3s ease;
            }

            .form-control-custom:focus {
                background-color: #ffffff;
                border-color: #6366f1;
                box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            }

            .btn-gradient {
                background: linear-gradient(135deg, #6366f1, #8b5cf6);
                border: none;
                color: white;
            }

            .btn-gradient:hover {
                background: linear-gradient(135deg, #4f46e5, #7c3aed);
                color: white;
            }
        </style>
    @endpush
</x-app-layout>
