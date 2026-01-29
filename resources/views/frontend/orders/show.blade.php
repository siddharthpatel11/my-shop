@extends('frontend.layouts.app')

@section('title', 'Order Details - ' . $order->order_number)

@section('content')
    <div class="container my-5">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('frontend.orders') }}">My Orders</a></li>
                <li class="breadcrumb-item active">{{ $order->order_number }}</li>
            </ol>
        </nav>

        {{-- Order Success Message --}}
        @if (session('order_success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Order Placed Successfully!</h5>
                        <p class="mb-0">Your order has been placed and is being processed.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Order Header --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h4 class="fw-bold mb-2">Order #{{ $order->order_number }}</h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar3 me-2"></i>
                            Placed on {{ $order->created_at->format('d M, Y \a\t h:i A') }}
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="mb-2">
                            <span class="badge bg-{{ $order->status_badge_color }} px-3 py-2 fs-6">
                                <i class="bi bi-box-seam me-1"></i>
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div>
                            <span class="badge bg-{{ $order->payment_status_badge_color }} px-3 py-2">
                                <i class="bi bi-credit-card me-1"></i>
                                Payment: {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Left Column --}}
            <div class="col-lg-8">
                {{-- Order Items --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-bag-check-fill text-primary me-2"></i>
                            Order Items ({{ $order->items->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        @foreach ($order->items as $item)
                            @php
                                $images = $item->product->image ? explode(',', $item->product->image) : [];
                                $firstImage = !empty($images) ? $images[0] : null;
                            @endphp
                            <div
                                class="d-flex align-items-start gap-3 pb-3 mb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                {{-- Product Image --}}
                                <div class="bg-light rounded overflow-hidden"
                                    style="width: 80px; height: 80px; flex-shrink: 0;">
                                    @if ($firstImage)
                                        <img src="{{ asset('images/products/' . $firstImage) }}"
                                            alt="{{ $item->product->name }}" class="img-fluid"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                            <i class="bi bi-image fs-3"></i>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $item->product->name }}</h6>
                                    <p class="text-muted small mb-2">
                                        {{ $item->product->category->name ?? '' }}
                                    </p>

                                    {{-- Color and Size --}}
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        @if ($item->color)
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="color-indicator"
                                                    style="background-color: {{ $item->color->hex_code }}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.2); display: inline-block;"></span>
                                                <small class="text-muted">{{ $item->color->name }}</small>
                                            </div>
                                        @endif

                                        @if ($item->size)
                                            <div class="d-flex align-items-center gap-1">
                                                <small class="text-muted">Size:</small>
                                                <span
                                                    class="badge bg-light text-dark border">{{ $item->size->code ?? $item->size->name }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">
                                            ₹{{ number_format($item->price, 2) }} × {{ $item->quantity }}
                                        </span>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <div class="fw-bold text-primary fs-6">
                                        ₹{{ number_format($item->subtotal, 2) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Delivery Address --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 p-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                            Delivery Address
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="bg-light rounded p-3">
                            <p class="mb-0 text-dark">
                                @if ($order->address->full_address)
                                    {{ $order->address->full_address }},<br>
                                @endif
                                {{ $order->address->city }}, {{ $order->address->district }},<br>
                                {{ $order->address->state }}, {{ $order->address->country }}
                                @if ($order->address->pincode)
                                    - {{ $order->address->pincode }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column - Order Summary --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white p-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-receipt me-2"></i>
                            Order Summary
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold">₹{{ number_format($order->subtotal, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Delivery Charges</span>
                            <span class="fw-semibold text-success">
                                {{ $order->shipping > 0 ? '₹' . number_format($order->shipping, 2) : 'FREE' }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Tax (GST)</span>
                            <span class="fw-semibold">₹{{ number_format($order->tax_amount, 2) }}</span>
                        </div>

                        {{-- Discount Display --}}
                        @if ($order->discount > 0)
                            <div class="bg-success bg-opacity-10 rounded p-3 mb-3 border border-success">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="d-block fw-bold text-success">
                                            <i class="bi bi-tag-fill me-1"></i>
                                            {{ $order->discount_code }}
                                        </small>
                                        <small class="text-muted">Discount Applied</small>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-3 text-success">
                                <span>Discount Savings</span>
                                <span class="fw-semibold">- ₹{{ number_format($order->discount, 2) }}</span>
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Total Amount</span>
                            <span class="fw-bold fs-5 text-primary">
                                ₹{{ number_format($order->total, 2) }}
                            </span>
                        </div>

                        <div class="bg-light rounded p-3 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-cash-coin fs-4 text-success me-3"></i>
                                <div>
                                    <small class="text-muted d-block">Payment Method</small>
                                    <span class="fw-semibold">Cash on Delivery</span>
                                </div>
                            </div>
                        </div>

                        @if ($order->discount > 0)
                            <div class="alert alert-success py-2 px-3 mb-3">
                                <small class="d-block mb-0">
                                    <i class="bi bi-gift-fill me-1"></i>
                                    You saved ₹{{ number_format($order->discount, 2) }} on this order!
                                </small>
                            </div>
                        @endif

                        <hr>

                        {{-- Action Buttons --}}
                        @if ($order->canBeCancelled())
                            <button class="btn btn-outline-danger w-100 mb-2" onclick="cancelOrder()">
                                <i class="bi bi-x-circle me-2"></i>
                                Cancel Order
                            </button>
                        @endif

                        <a href="{{ route('frontend.orders') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left me-2"></i>
                            Back to Orders
                        </a>

                        <div class="mt-4 pt-3 border-top">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-info-circle me-1"></i>
                                Need help with your order?
                            </small>
                            <a href="#" class="btn btn-sm btn-link p-0">Contact Support</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel Order Modal --}}
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="cancelOrderModalLabel">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this order?</p>
                    <p class="text-muted small mb-0">
                        Order #{{ $order->order_number }}
                    </p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Order</button>
                    <button type="button" class="btn btn-danger" onclick="confirmCancelOrder()">
                        Yes, Cancel Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cancelOrder() {
            const modal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
            modal.show();
        }

        function confirmCancelOrder() {
            fetch("{{ route('frontend.order.cancel', $order->id) }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to cancel order');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error cancelling order');
                });
        }
    </script>

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endsection
