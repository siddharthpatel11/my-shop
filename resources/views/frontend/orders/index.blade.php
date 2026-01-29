@extends('frontend.layouts.app')

@section('title', 'My Orders')

@section('content')
    <div class="container my-5">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item active">My Orders</li>
            </ol>
        </nav>

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">
                    <i class="bi bi-bag-check-fill text-primary me-2"></i> My Orders
                </h2>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="row g-2">
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter" onchange="filterOrders()">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="paymentFilter" onchange="filterOrders()">
                            <option value="">All Payments</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" id="searchOrder" placeholder="Search by order number..."
                            onkeyup="searchOrders()">
                    </div>
                </div>
            </div>
        </div>

        {{-- Orders List --}}
        <div id="ordersList">
            @forelse($orders as $order)
                <div class="card border-0 shadow-sm mb-3 order-card" data-status="{{ $order->status }}"
                    data-payment="{{ $order->payment_status }}" data-order-number="{{ $order->order_number }}">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            {{-- Order Info --}}
                            <div class="col-lg-3 mb-3 mb-lg-0">
                                <h6 class="fw-bold mb-1">{{ $order->order_number }}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $order->created_at->format('d M, Y') }}
                                </small>
                            </div>

                            {{-- Items Count --}}
                            <div class="col-lg-2 col-6 mb-3 mb-lg-0">
                                <small class="text-muted d-block">Items</small>
                                <span class="fw-semibold">{{ $order->items->count() }} item(s)</span>
                            </div>

                            {{-- Total Amount --}}
                            <div class="col-lg-2 col-6 mb-3 mb-lg-0">
                                <small class="text-muted d-block">Total</small>
                                <span class="fw-bold text-primary">₹{{ number_format($order->total, 2) }}</span>
                                @if ($order->discount > 0)
                                    <br>
                                    <small class="text-success">
                                        <i class="bi bi-tag-fill"></i> -₹{{ number_format($order->discount, 2) }}
                                    </small>
                                @endif
                            </div>

                            {{-- Status Badges --}}
                            <div class="col-lg-3 col-12 mb-3 mb-lg-0">
                                <div class="mb-2">
                                    <span class="badge bg-{{ $order->status_badge_color }} px-3 py-2">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div>
                                    <small class="text-muted">Payment:</small>
                                    <span class="badge bg-{{ $order->payment_status_badge_color }} px-2 py-1">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="col-lg-2 col-12 text-lg-end">
                                <a href="{{ route('frontend.order.show', $order->id) }}"
                                    class="btn btn-sm btn-primary w-100 w-lg-auto">
                                    <i class="bi bi-eye me-1"></i> View Details
                                </a>
                            </div>
                        </div>

                        {{-- Order Items Preview --}}
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ($order->items->take(3) as $item)
                                    @php
                                        $images = $item->product->image ? explode(',', $item->product->image) : [];
                                        $firstImage = !empty($images) ? $images[0] : null;
                                    @endphp
                                    <div class="order-item-preview" style="width: 50px; height: 50px;">
                                        @if ($firstImage)
                                            <img src="{{ asset('images/products/' . $firstImage) }}"
                                                alt="{{ $item->product->name }}" class="img-fluid rounded"
                                                style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                        {{-- Product Name --}}

                                    </div>
                                    <div>
                                        <small class="text-muted d-block text-truncate" style="max-width: 100%;"
                                            title="{{ $item->product->name }}">
                                            {{ $item->product->name }}
                                        </small>
                                        <small class="text-muted d-block text-truncate" style="max-width: 100%;"
                                            title="{{ $item->product->category->name }}">
                                            {{ $item->product->category->name }}
                                        </small>
                                    </div>
                                @endforeach
                                @if ($order->items->count() > 3)
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center px-2"
                                        style="width: 50px; height: 50px;">
                                        <small class="fw-bold">+{{ $order->items->count() - 3 }}</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-bag-x fs-1 text-muted mb-3"></i>
                        <h4 class="text-muted mb-3">No Orders Found</h4>
                        <p class="text-muted mb-4">You haven't placed any orders yet.</p>
                        <a href="{{ route('frontend.products.index') }}" class="btn btn-primary">
                            <i class="bi bi-shop me-2"></i> Start Shopping
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <style>
        .order-card {
            transition: all 0.3s ease;
        }

        .order-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
            transform: translateY(-2px);
        }

        .order-item-preview {
            transition: transform 0.2s;
        }

        .order-item-preview:hover {
            transform: scale(1.1);
        }
    </style>

    <script>
        function filterOrders() {
            const statusFilter = document.getElementById('statusFilter').value;
            const paymentFilter = document.getElementById('paymentFilter').value;
            const orders = document.querySelectorAll('.order-card');

            orders.forEach(order => {
                const status = order.getAttribute('data-status');
                const payment = order.getAttribute('data-payment');

                let showOrder = true;

                if (statusFilter && status !== statusFilter) {
                    showOrder = false;
                }

                if (paymentFilter && payment !== paymentFilter) {
                    showOrder = false;
                }

                order.style.display = showOrder ? 'block' : 'none';
            });

            checkEmptyState();
        }

        function searchOrders() {
            const searchTerm = document.getElementById('searchOrder').value.toLowerCase();
            const orders = document.querySelectorAll('.order-card');

            orders.forEach(order => {
                const orderNumber = order.getAttribute('data-order-number').toLowerCase();
                order.style.display = orderNumber.includes(searchTerm) ? 'block' : 'none';
            });

            checkEmptyState();
        }

        function checkEmptyState() {
            const orders = document.querySelectorAll('.order-card');
            const visibleOrders = Array.from(orders).filter(order => order.style.display !== 'none');

            let emptyState = document.getElementById('emptyState');

            if (visibleOrders.length === 0 && !emptyState) {
                emptyState = document.createElement('div');
                emptyState.id = 'emptyState';
                emptyState.className = 'card border-0 shadow-sm';
                emptyState.innerHTML = `
                    <div class="card-body text-center py-5">
                        <i class="bi bi-search fs-1 text-muted mb-3"></i>
                        <h4 class="text-muted mb-3">No Orders Found</h4>
                        <p class="text-muted">Try adjusting your filters or search term.</p>
                    </div>
                `;
                document.getElementById('ordersList').appendChild(emptyState);
            } else if (visibleOrders.length > 0 && emptyState) {
                emptyState.remove();
            }
        }
    </script>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endsection
