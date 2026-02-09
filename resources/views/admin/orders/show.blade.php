@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
                <li class="breadcrumb-item active">{{ $order->order_number }}</li>
            </ol>
        </nav>

        {{-- Delivery Status Alert --}}
        @if ($order->hasPartialDelivery())
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h5 class="alert-heading">⚠️ Partial Delivery</h5>
                <p class="mb-0">{{ $order->getDeliveryStatusMessage() }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        {{-- Order Header --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h4 class="fw-bold mb-2">Order #{{ $order->order_number }}</h4>
                        <p class="mb-1">
                            <span class="badge bg-{{ $order->order_status_badge_color }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                            {{--  <span class="badge bg-{{ $item->item_status === 'delivered' ? 'success' : 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $item->item_status)) }}
                            </span>  --}}

                            <span class="badge bg-{{ $order->getPaymentStatusBadgeColorAttribute() }} ms-2">
                                Payment: {{ ucfirst($order->payment_status) }}
                            </span>
                        </p>
                        <p class="text-muted mb-0">
                            <i class="fas fa-calendar me-2"></i>
                            {{ $order->created_at->format('d M, Y \a\t h:i A') }}
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Left Column --}}
            <div class="col-lg-8">
                {{-- Customer Information --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-user text-primary me-2"></i>Customer Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Name:</strong> {{ $order->customer->name }}</p>
                                <p class="mb-2"><strong>Email:</strong> {{ $order->customer->email }}</p>
                                <p class="mb-0"><strong>Phone:</strong> {{ $order->customer->phone ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Customer ID:</strong> #{{ $order->customer->id }}</p>
                                <p class="mb-0">
                                    <strong>Member Since:</strong>
                                    {{ $order->customer->created_at->format('d M, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Purchased Products with Status Management --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-shopping-bag text-primary me-2"></i>
                            Purchased Products ({{ $order->activeItems()->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <form action="{{ route('admin.orders.process-partial-delivery', $order->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th>Variant</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Price</th>
                                            <th>Order Status</th>
                                            <th>Item Status</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->activeItems as $index => $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @php
                                                            $images = $item->product->image
                                                                ? explode(',', $item->product->image)
                                                                : [];
                                                            $firstImage = !empty($images) ? $images[0] : null;
                                                        @endphp
                                                        <div class="me-3">
                                                            @if ($firstImage)
                                                                <img src="{{ asset('images/products/' . $firstImage) }}"
                                                                    alt="{{ $item->product->name }}"
                                                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                            @else
                                                                <div class="bg-light d-flex align-items-center justify-content-center"
                                                                    style="width: 50px; height: 50px; border-radius: 5px;">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <strong>{{ $item->product->name }}</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($item->color)
                                                        <div class="mb-1">
                                                            <span
                                                                style="display: inline-block; width: 16px; height: 16px; border-radius: 50%; background-color: {{ $item->color->hex_code }}; border: 2px solid #ddd;"></span>
                                                            <small>{{ $item->color->name }}</small>
                                                        </div>
                                                    @endif
                                                    @if ($item->size)
                                                        <small class="badge bg-light text-dark border">
                                                            {{ $item->size->code ?? $item->size->name }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <strong>₹{{ number_format($item->subtotal, 2) }}</strong>
                                                </td>
                                                <td style="width: 150px;">
                                                    <span class="badge bg-{{ $order->order_status_badge_color }}">
                                                        @if ($order->order_status == 'pending')
                                                            🕐 Pending
                                                        @elseif($order->order_status == 'processing')
                                                            ⚙️ Processing
                                                        @elseif($order->order_status == 'shipped')
                                                            🚚 Shipped
                                                        @elseif($order->order_status == 'delivered')
                                                            ✅ Delivered
                                                        @elseif($order->order_status == 'cancelled')
                                                            ❌ Cancelled
                                                        @endif
                                                    </span>
                                                </td>
                                                <td style="width: 180px;">
                                                    <input type="hidden" name="items[{{ $index }}][id]"
                                                        value="{{ $item->id }}">
                                                    <select name="items[{{ $index }}][item_status]"
                                                        class="form-select form-select-sm" style="font-size: 0.875rem;">
                                                        <option value="pending"
                                                            {{ $item->item_status == 'pending' ? 'selected' : '' }}>
                                                            ⏳ Pending
                                                        </option>
                                                        <option value="available"
                                                            {{ $item->item_status == 'available' ? 'selected' : '' }}>
                                                            ✅ Available
                                                        </option>
                                                        <option value="out_of_stock"
                                                            {{ $item->item_status == 'out_of_stock' ? 'selected' : '' }}>
                                                            ❌ Out of Stock
                                                        </option>
                                                        <option value="delivered"
                                                            {{ $item->item_status == 'delivered' ? 'selected' : '' }}>
                                                            📦 Delivered
                                                        </option>
                                                        <option value="cancelled"
                                                            {{ $item->item_status == 'cancelled' ? 'selected' : '' }}>
                                                            🚫 Cancelled
                                                        </option>
                                                    </select>
                                                </td>
                                                <td style="width: 250px;">
                                                    <input type="text" name="items[{{ $index }}][notes]"
                                                        class="form-control form-control-sm"
                                                        placeholder="Add note (optional)" value="{{ $item->notes }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer bg-white border-0 py-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check me-2"></i>Update Item Status & Notify Customer
                                </button>
                                <small class="text-muted ms-3">
                                    <i class="fas fa-info-circle"></i> This will send an email to the customer if there's a
                                    partial delivery.
                                </small>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Delivery Address --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>Delivery Address
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
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

                {{-- Order Notes --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-sticky-note text-primary me-2"></i>Order Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.orders.add-notes', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <textarea name="notes" class="form-control" rows="3" placeholder="Add internal notes about this order...">{{ $order->notes }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Notes
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="col-lg-4">
                {{-- Order Status Management --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-tasks me-2"></i>Order Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label fw-bold">Order Status</label>
                                <select name="order_status" class="form-select form-select-lg">
                                    <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>
                                        🕐 Pending
                                    </option>
                                    <option value="processing"
                                        {{ $order->order_status == 'processing' ? 'selected' : '' }}>
                                        ⚙️ Processing
                                    </option>
                                    <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>
                                        🚚 Shipped
                                    </option>
                                    <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>
                                        ✅ Delivered
                                    </option>
                                    <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>
                                        ❌ Cancelled
                                    </option>
                                </select>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle"></i> Changing this will update ALL items in the order
                                </small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check me-2"></i>Update Status & Notify Customer
                            </button>
                        </form>

                        <hr>

                        <form action="{{ route('admin.orders.update-payment-status', $order->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label class="form-label fw-bold">Payment Status</label>
                                <select name="payment_status" class="form-select form-select-lg">
                                    <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>
                                        🕐 Pending
                                    </option>
                                    <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>
                                        💰 Paid
                                    </option>
                                    <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>
                                        ❌ Failed
                                    </option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-money-check-alt me-2"></i>Update Payment
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-receipt text-primary me-2"></i>Order Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="fw-semibold">₹{{ number_format($order->subtotal, 2) }}</span>
                        </div>

                        @if ($order->tax)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tax ({{ $order->tax->name }}):</span>
                                <span class="fw-semibold">₹{{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Shipping:</span>
                            <span class="fw-semibold text-success">
                                {{ $order->shipping > 0 ? '₹' . number_format($order->shipping, 2) : 'FREE' }}
                            </span>
                        </div>

                        @if ($order->discount > 0)
                            <div class="bg-success bg-opacity-10 rounded p-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <span class="text-success">
                                        <i class="fas fa-tag me-1"></i>Discount
                                        @if ($order->discount_code)
                                            ({{ $order->discount_code }})
                                        @endif
                                    </span>
                                    <span class="fw-semibold text-success">
                                        -₹{{ number_format($order->discount, 2) }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold fs-5">Total:</span>
                            <span class="fw-bold fs-5 text-primary">
                                ₹{{ number_format($order->total, 2) }}
                            </span>
                        </div>

                        <div class="bg-light rounded p-3">
                            <small class="text-muted d-block mb-1">Payment Method</small>
                            <strong>{{ strtoupper($order->payment_method) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                alert('{{ session('success') }}');
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                alert('ERROR: {{ session('error') }}');
            });
        </script>
    @endif

    @if (session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                alert('WARNING: {{ session('warning') }}');
            });
        </script>
    @endif
@endsection
