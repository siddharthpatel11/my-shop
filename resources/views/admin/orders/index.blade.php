@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">
                    <i class="fas fa-shopping-bag me-2"></i>Customer Orders Management
                </h2>
                <p class="text-muted">View and manage all customer orders, payment status, and order details</p>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Total Orders</h6>
                                <h3 class="mb-0">{{ number_format($stats['total_orders']) }}</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Pending Orders</h6>
                                <h3 class="mb-0">{{ number_format($stats['pending_orders']) }}</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Total Revenue</h6>
                                <h3 class="mb-0">₹{{ number_format($stats['total_revenue'], 2) }}</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-rupee-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white-50 mb-1">Pending Payments</h6>
                                <h3 class="mb-0">₹{{ number_format($stats['pending_payments'], 2) }}</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="fas fa-money-bill-wave fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control"
                            placeholder="Order number or customer name..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Order Status</label>
                        <select name="order_status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="processing" {{ request('order_status') == 'processing' ? 'selected' : '' }}>
                                Processing</option>
                            <option value="shipped" {{ request('order_status') == 'shipped' ? 'selected' : '' }}>Shipped
                            </option>
                            <option value="delivered" {{ request('order_status') == 'delivered' ? 'selected' : '' }}>
                                Delivered</option>
                            <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>
                                Cancelled</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-select">
                            <option value="">All Payments</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid
                            </option>
                            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </form>

                <div class="mt-3">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-redo"></i> Clear Filters
                    </a>
                    <a href="{{ route('admin.orders.export', request()->all()) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel"></i> Export to CSV
                    </a>
                </div>
            </div>
        </div>

        {{-- Orders Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @if ($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Order Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>
                                            <strong>{{ $order->order_number }}</strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $order->customer->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $order->customer->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <small>
                                                {{ $order->created_at->format('d M, Y') }}<br>
                                                {{ $order->created_at->format('h:i A') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ $order->items->count() }} item(s)
                                            </span>
                                            @if ($order->hasPartialDelivery())
                                                <br><small class="text-warning">
                                                    <i class="fas fa-exclamation-triangle"></i> Partial
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-primary">₹{{ number_format($order->total, 2) }}</strong>
                                            @if ($order->discount > 0)
                                                <br>
                                                <small class="text-success">
                                                    <i class="fas fa-tag"></i> -₹{{ number_format($order->discount, 2) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.orders.update-payment-status', $order->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <select name="payment_status"
                                                    class="form-select form-select-sm payment-status-select"
                                                    onchange="this.form.submit()" style="width: 120px;">
                                                    <option value="pending"
                                                        {{ $order->payment_status == 'pending' ? 'selected' : '' }}>
                                                        Pending
                                                    </option>
                                                    <option value="paid"
                                                        {{ $order->payment_status == 'paid' ? 'selected' : '' }}>
                                                        Paid
                                                    </option>
                                                    <option value="failed"
                                                        {{ $order->payment_status == 'failed' ? 'selected' : '' }}>
                                                        Failed
                                                    </option>
                                                </select>
                                            </form>
                                            <br>
                                            <small class="text-muted">{{ ucfirst($order->payment_method) }}</small>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.orders.update-status', $order->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('This will update all order items and send email notification to customer. Continue?');">
                                                @csrf
                                                @method('PATCH')
                                                <select name="order_status"
                                                    class="form-select form-select-sm status-select"
                                                    onchange="this.form.submit()" style="width: 130px;">
                                                    <option value="pending"
                                                        {{ $order->order_status == 'pending' ? 'selected' : '' }}>
                                                        🕐 Pending
                                                    </option>
                                                    <option value="processing"
                                                        {{ $order->order_status == 'processing' ? 'selected' : '' }}>
                                                        ⚙️ Processing
                                                    </option>
                                                    <option value="shipped"
                                                        {{ $order->order_status == 'shipped' ? 'selected' : '' }}>
                                                        🚚 Shipped
                                                    </option>
                                                    <option value="delivered"
                                                        {{ $order->order_status == 'delivered' ? 'selected' : '' }}>
                                                        ✅ Delivered
                                                    </option>
                                                    <option value="cancelled"
                                                        {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>
                                                        ❌ Cancelled
                                                    </option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                                class="btn btn-sm btn-info text-white" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Orders Found</h5>
                        <p class="text-muted">No orders match your filter criteria.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .status-select {
            cursor: pointer;
        }

        .status-select:hover {
            border-color: #0d6efd;
        }

        .payment-status-select {
            cursor: pointer;
        }

        .payment-status-select:hover {
            border-color: #198754;
        }
    </style>

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
