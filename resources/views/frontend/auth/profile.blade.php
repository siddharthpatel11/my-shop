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
                                        <div class="col-7">
                                            <p class="fw-semibold mb-0">{{ $customer->email }}</p>
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

    <style>
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
        // Update cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            const cart = JSON.parse(localStorage.getItem('shoppingCart') || '[]');
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            document.getElementById('cartItemsCount').textContent = totalItems;
        });
    </script>
@endsection
