@extends('layouts.frontend.app')

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->
    <div class="hero-section position-relative overflow-hidden mb-5 rounded-4">
        <div class="hero-bg position-absolute top-0 start-0 w-100 h-100"
            style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); opacity: 0.1;"></div>
        <div class="container position-relative py-5 py-md-5">
            <div class="row align-items-center py-5">
                <div class="col-lg-6 text-center text-lg-start">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-3">
                        Summer Collection 2026
                    </span>
                    <h1 class="display-3 fw-bold mb-4">Style Meets <span class="text-primary">Comfort</span></h1>
                    <p class="lead text-muted mb-5">Explore our curated collection of premium products designed for your
                        lifestyle. High quality, great prices, and fast delivery.</p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        <a href="{{ route('frontend.products.index') }}"
                            class="btn btn-primary btn-lg px-5 py-3 rounded-3 shadow-sm">
                            Shop Now <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        <a href="#categories" class="btn btn-outline-dark btn-lg px-5 py-3 rounded-3">
                            View Categories
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="hero-image-wrapper p-4">
                        <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=800&q=80"
                            alt="Hero Image" class="img-fluid rounded-4 shadow-lg scale-hover transition">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Categories -->
    <div class="container mb-5" id="categories">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold mb-0">Featured Categories</h2>
                <p class="text-muted mb-0">Find exactly what you're looking for</p>
            </div>
        </div>
        <div class="row g-4">
            @foreach ($categories->take(4) as $category)
                <div class="col-6 col-md-3">
                    <a href="{{ route('frontend.products.index', ['category' => $category->id]) }}"
                        class="text-decoration-none">
                        <div
                            class="category-card card border-0 shadow-sm rounded-4 overflow-hidden h-100 text-center p-4 transition-up">
                            <div class="category-icon mb-3">
                                <i class="fas fa-th-large fa-3x text-primary opacity-75"></i>
                            </div>
                            <h5 class="fw-bold mb-0 text-dark">{{ $category->name }}</h5>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Latest Products -->
    <div class="container mb-5">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h2 class="fw-bold mb-0">Latest Arrivals</h2>
                <p class="text-muted mb-0">Our newest additions to the shop</p>
            </div>
            <a href="{{ route('frontend.products.index') }}" class="btn btn-link text-decoration-none fw-bold">
                View All <i class="fas fa-chevron-right ms-1 small"></i>
            </a>
        </div>
        <div class="row g-4">
            @foreach ($latestProducts as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-card transition-up">
                        <div class="position-relative">
                            @php
                                $imageUrl = $product->image
                                    ? asset('uploads/products/' . $product->image)
                                    : 'https://via.placeholder.com/400x400?text=' . $product->name;
                            @endphp
                            <img src="{{ $imageUrl }}" class="card-img-top p-3 rounded-5" alt="{{ $product->name }}"
                                style="height: 250px; object-fit: cover;">
                            <button
                                class="btn btn-white position-absolute top-0 end-0 m-3 rounded-circle shadow-sm wishlist-btn"
                                onclick="addToWishlist({{ $product->id }})">
                                <i class="far fa-heart text-danger"></i>
                            </button>
                        </div>
                        <div class="card-body p-4 pt-0 text-center">
                            <small
                                class="text-primary fw-bold text-uppercase mb-1 d-block">{{ $product->category->name ?? 'Uncategorized' }}</small>
                            <h5 class="card-title fw-bold mb-2 h6">{{ $product->name }}</h5>
                            <div class="d-flex justify-content-center align-items-center mb-3">
                                <span class="h5 fw-bold text-dark mb-0">₹{{ number_format($product->price, 2) }}</span>
                            </div>
                            <a href="{{ route('frontend.products.show', $product->id) }}"
                                class="btn btn-outline-primary w-100 rounded-3 py-2">
                                <i class="fas fa-eye me-2"></i>Quick View
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-light py-5 rounded-4 mb-5">
        <div class="container">
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="p-3">
                        <i class="fas fa-truck fa-2x text-primary mb-3"></i>
                        <h6 class="fw-bold">Free Shipping</h6>
                        <p class="small text-muted mb-0">On all orders over ₹999</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <i class="fas fa-undo fa-2x text-primary mb-3"></i>
                        <h6 class="fw-bold">Easy Returns</h6>
                        <p class="small text-muted mb-0">30 days return policy</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <i class="fas fa-lock fa-2x text-primary mb-3"></i>
                        <h6 class="fw-bold">Secure Payment</h6>
                        <p class="small text-muted mb-0">100% secure checkout</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <i class="fas fa-headset fa-2x text-primary mb-3"></i>
                        <h6 class="fw-bold">24/7 Support</h6>
                        <p class="small text-muted mb-0">Dedicated support team</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .transition {
            transition: all 0.3s ease;
        }

        .transition-up:hover {
            transform: translateY(-10px);
        }

        .scale-hover:hover {
            transform: scale(1.02);
        }

        .product-card .wishlist-btn {
            background: white;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .product-card:hover .wishlist-btn {
            opacity: 1;
            transform: translateY(0);
        }

        .category-card:hover {
            background: #6366f1 !important;
        }

        .category-card:hover h5,
        .category-card:hover i {
            color: white !important;
        }

        .hero-section {
            min-height: 500px;
            display: flex;
            align-items: center;
        }
    </style>
@endsection
