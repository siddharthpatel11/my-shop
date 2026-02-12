@extends('layouts.frontend.app')

@section('title', 'My Panel')

@section('content')
    <div class="container my-5">
        <div class="row">
            <div class="col-12 mb-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                        <li class="breadcrumb-item active">My Panel</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="fw-bold mb-1">Welcome, {{ auth('customer')->user()->name }}!</h2>
                        <p class="text-muted">Manage your orders and saved items in one place.</p>
                    </div>
                    <div class="d-none d-md-block">
                        <i class="fas fa-th-large fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Overview --}}
        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center p-4 h-100">
                    <div class="card-icon bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3"
                        style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-receipt fa-xl"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalOrders }}</h3>
                    <p class="text-muted mb-0 small uppercase fw-bold">Total Orders</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm text-center p-4 h-100">
                    <div class="card-icon bg-danger bg-opacity-10 text-danger rounded-circle mx-auto mb-3"
                        style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-heart fa-xl"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalWishlist }}</h3>
                    <p class="text-muted mb-0 small uppercase fw-bold">Wishlist Items</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Recent Orders Section --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0">Recent Orders</h5>
                        <a href="{{ route('frontend.orders') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        @if ($recentOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 border-0">Order ID</th>
                                            <th class="border-0">Date</th>
                                            <th class="border-0">Total</th>
                                            <th class="border-0">Status</th>
                                            <th class="border-0 text-end pe-4">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentOrders as $order)
                                            <tr>
                                                <td class="ps-4">#{{ $order->id }}</td>
                                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                                <td class="fw-bold">₹{{ number_format($order->total_price, 2) }}</td>
                                                <td>
                                                    <span
                                                        class="badge rounded-pill bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'info') }}">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="{{ route('frontend.order.show', $order->id) }}"
                                                        class="btn btn-sm btn-light rounded-pill">
                                                        <i class="fas fa-eye text-primary"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <p class="text-muted mb-0">No orders found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Wishlist Preview Section --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold mb-0">My Wishlist</h5>
                        <a href="{{ route('frontend.wishlist') }}" class="btn btn-sm btn-outline-danger">Manage</a>
                    </div>
                    <div class="card-body p-0">
                        @if ($wishlistItems->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($wishlistItems as $item)
                                    @php $images = $item->product->image ? explode(',', $item->product->image) : []; @endphp
                                    <div class="list-group-item border-0 px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('images/products/' . ($images[0] ?? 'no-image.png')) }}"
                                                alt="{{ $item->product->name }}" class="rounded me-3 border"
                                                style="width: 50px; height: 50px; object-fit: contain;">
                                            <div class="flex-grow-1 overflow-hidden">
                                                <h6 class="text-truncate mb-1 fw-bold">{{ $item->product->name }}</h6>
                                                <p class="text-primary fw-bold mb-0 small">
                                                    ₹{{ number_format($item->product->price, 2) }}</p>
                                            </div>
                                            <a href="{{ route('frontend.products.show', $item->product->id) }}"
                                                class="btn btn-sm btn-light rounded-pill ms-2">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <p class="text-muted mb-0">Your wishlist is empty.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card-icon {
            transition: all 0.3s ease;
        }

        .card:hover .card-icon {
            transform: scale(1.1);
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: var(--bs-primary);
        }

        .uppercase {
            letter-spacing: 1px;
            font-size: 0.7rem !important;
        }
    </style>
@endsection
