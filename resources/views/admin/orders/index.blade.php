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

                <div class="mt-3 d-flex gap-2 align-items-center flex-wrap">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-redo"></i> Clear Filters
                    </a>
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                        data-bs-target="#exportModal">
                        <i class="fas fa-file-export me-1"></i> Export Orders
                    </button>
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
                                        <td><strong>{{ $order->order_number }}</strong></td>
                                        <td>
                                            <strong>{{ $order->customer->name }}</strong><br>
                                            <small class="text-muted">{{ $order->customer->email }}</small>
                                        </td>
                                        <td>
                                            <small>
                                                {{ $order->created_at->format('d M, Y') }}<br>
                                                {{ $order->created_at->format('h:i A') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $order->items->count() }} item(s)</span>
                                            @if ($order->hasPartialDelivery())
                                                <br><small class="text-warning">
                                                    <i class="fas fa-exclamation-triangle"></i> Partial
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-primary">₹{{ number_format($order->total, 2) }}</strong>
                                            @if ($order->discount > 0)
                                                <br><small class="text-success">
                                                    <i class="fas fa-tag"></i> -₹{{ number_format($order->discount, 2) }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.orders.update-payment-status', $order->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <select name="payment_status" class="form-select form-select-sm"
                                                    onchange="this.form.submit()" style="width:120px;">
                                                    <option value="pending"
                                                        {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending
                                                    </option>
                                                    <option value="paid"
                                                        {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid
                                                    </option>
                                                    <option value="failed"
                                                        {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed
                                                    </option>
                                                </select>
                                            </form>
                                            <br><small class="text-muted">{{ ucfirst($order->payment_method) }}</small>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.orders.update-status', $order->id) }}"
                                                method="POST" class="d-inline status-form">
                                                @csrf
                                                @method('PATCH')
                                                <select name="order_status"
                                                    class="form-select form-select-sm status-select"
                                                    onchange="this.form.submit()" style="width:130px;">
                                                    <option value="pending"
                                                        {{ $order->order_status == 'pending' ? 'selected' : '' }}>🕐
                                                        Pending</option>
                                                    <option value="processing"
                                                        {{ $order->order_status == 'processing' ? 'selected' : '' }}>⚙️
                                                        Processing</option>
                                                    <option value="shipped"
                                                        {{ $order->order_status == 'shipped' ? 'selected' : '' }}>🚚
                                                        Shipped</option>
                                                    <option value="delivered"
                                                        {{ $order->order_status == 'delivered' ? 'selected' : '' }}>✅
                                                        Delivered</option>
                                                    <option value="cancelled"
                                                        {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>❌
                                                        Cancelled</option>
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

    {{-- ════════════════════════════════════════════════
         EXPORT MODAL
         ════════════════════════════════════════════════ --}}
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:560px;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                {{-- Header --}}
                <div class="modal-header border-0 px-4 pt-4 pb-3"
                    style="background:linear-gradient(135deg,#1a7f4b 0%,#28a86a 100%);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:44px;height:44px;background:rgba(255,255,255,.2);">
                            <i class="fas fa-file-export text-white"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white fw-bold mb-0" id="exportModalLabel">
                                Export Orders
                            </h5>
                            <small class="text-white-50">Set filters, then pick your format</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                {{-- Body --}}
                <div class="modal-body px-4 py-4" style="background:#f8fafb;">

                    @if (request()->hasAny(['order_status', 'payment_status', 'date_from', 'date_to', 'search']))
                        <div class="alert alert-info d-flex align-items-center gap-2 py-2 mb-3"
                            style="font-size:.84rem;border-radius:10px;">
                            <i class="fas fa-info-circle flex-shrink-0"></i>
                            <span>Your current page filters have been pre-filled below.</span>
                        </div>
                    @endif

                    <div class="row g-3">

                        {{-- Order Status --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold small mb-1">
                                <i class="fas fa-tag me-1 text-success"></i>Order Status
                            </label>
                            <select id="exp_order_status" class="form-select form-select-sm">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('order_status') == 'pending' ? 'selected' : '' }}>🕐
                                    Pending</option>
                                <option value="processing"
                                    {{ request('order_status') == 'processing' ? 'selected' : '' }}>⚙️ Processing</option>
                                <option value="shipped" {{ request('order_status') == 'shipped' ? 'selected' : '' }}>🚚
                                    Shipped</option>
                                <option value="delivered" {{ request('order_status') == 'delivered' ? 'selected' : '' }}>
                                    ✅ Delivered</option>
                                <option value="cancelled" {{ request('order_status') == 'cancelled' ? 'selected' : '' }}>
                                    ❌ Cancelled</option>
                            </select>
                        </div>

                        {{-- Payment Status --}}
                        <div class="col-6">
                            <label class="form-label fw-semibold small mb-1">
                                <i class="fas fa-credit-card me-1 text-success"></i>Payment Status
                            </label>
                            <select id="exp_payment_status" class="form-select form-select-sm">
                                <option value="">All Payments</option>
                                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>
                                    Pending</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid
                                </option>
                                <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>
                                    Failed</option>
                            </select>
                        </div>

                        {{-- Time Span --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold small mb-1">
                                <i class="fas fa-calendar-alt me-1 text-success"></i>Time Span
                            </label>
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                <button type="button" class="btn btn-outline-secondary preset-btn py-1 px-2"
                                    style="font-size:.75rem;" data-preset="today">Today</button>
                                <button type="button" class="btn btn-outline-secondary preset-btn py-1 px-2"
                                    style="font-size:.75rem;" data-preset="7">Last 7 days</button>
                                <button type="button" class="btn btn-outline-secondary preset-btn py-1 px-2"
                                    style="font-size:.75rem;" data-preset="30">Last 30 days</button>
                                <button type="button" class="btn btn-outline-secondary preset-btn py-1 px-2"
                                    style="font-size:.75rem;" data-preset="90">Last 3 months</button>
                                <button type="button" class="btn btn-outline-secondary preset-btn py-1 px-2"
                                    style="font-size:.75rem;" data-preset="all">All time</button>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1" style="font-size:.78rem;">From</label>
                                    <input type="date" id="exp_date_from" class="form-control form-control-sm"
                                        value="{{ request('date_from') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label text-muted mb-1" style="font-size:.78rem;">To</label>
                                    <input type="date" id="exp_date_to" class="form-control form-control-sm"
                                        value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Live summary --}}
                        <div class="col-12">
                            <div class="rounded-3 p-2 px-3 d-flex align-items-start gap-2"
                                style="background:#e7f6ee;border:1px solid #b8dfc9;font-size:.82rem;">
                                <i class="fas fa-filter text-success mt-1 flex-shrink-0"></i>
                                <span id="exp_summary" class="text-success fw-semibold">
                                    All orders will be exported.
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="modal-footer border-0 px-4 pb-4 pt-2 gap-2 justify-content-between"
                    style="background:#f8fafb;">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <div class="d-flex gap-2">
                        <a id="btn_csv" href="#" class="btn btn-sm btn-outline-success fw-semibold"
                            style="border-radius:8px;">
                            <i class="fa-solid fa-file-csv me-1"></i>Download CSV
                        </a>
                        <a id="btn_excel" href="#" class="btn btn-sm btn-success fw-semibold"
                            style="border-radius:8px;">
                            <i class="fa-solid fa-file-excel me-1"></i>Download Excel
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // ── Order status SweetAlert ───────────────────────────────────────────
            document.querySelectorAll('.status-form select').forEach(function(select) {
                var orig = select.value;
                select.addEventListener('change', function() {
                    var form = this.closest('form');
                    var self = this;
                    Swal.fire({
                        title: 'Update Status?',
                        text: 'This will update all order items and send an email to the customer.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#aaa',
                        confirmButtonText: 'Yes, update it!'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        } else {
                            self.value = orig;
                        }
                    });
                });
            });

            // ── Export modal logic ────────────────────────────────────────────────
            (function() {
                var BASE = '{{ route('admin.orders.export') }}';

                var selOrder = document.getElementById('exp_order_status');
                var selPayment = document.getElementById('exp_payment_status');
                var inpFrom = document.getElementById('exp_date_from');
                var inpTo = document.getElementById('exp_date_to');
                var btnCsv = document.getElementById('btn_csv');
                var btnExcel = document.getElementById('btn_excel');
                var summary = document.getElementById('exp_summary');

                // Build query string for a given format
                function buildUrl(fmt) {
                    var p = new URLSearchParams({
                        format: fmt
                    });
                    if (selOrder.value) p.set('order_status', selOrder.value);
                    if (selPayment.value) p.set('payment_status', selPayment.value);
                    if (inpFrom.value) p.set('date_from', inpFrom.value);
                    if (inpTo.value) p.set('date_to', inpTo.value);
                    return BASE + '?' + p.toString();
                }

                // Update button hrefs + summary text
                function refresh() {
                    btnCsv.href = buildUrl('csv');
                    btnExcel.href = buildUrl('excel');

                    var parts = [];
                    if (selOrder.value) {
                        var t = selOrder.options[selOrder.selectedIndex].text.replace(/^\S+\s*/, '');
                        parts.push('Order: <strong>' + t + '</strong>');
                    }
                    if (selPayment.value) {
                        parts.push('Payment: <strong>' + selPayment.options[selPayment.selectedIndex].text + '</strong>');
                    }
                    if (inpFrom.value && inpTo.value) {
                        parts.push('<strong>' + inpFrom.value + '</strong> → <strong>' + inpTo.value + '</strong>');
                    } else if (inpFrom.value) {
                        parts.push('From <strong>' + inpFrom.value + '</strong>');
                    } else if (inpTo.value) {
                        parts.push('Until <strong>' + inpTo.value + '</strong>');
                    }

                    summary.innerHTML = parts.length ?
                        'Exporting: ' + parts.join(' &middot; ') :
                        'All orders will be exported.';
                }

                // Wire up filter inputs
                [selOrder, selPayment, inpFrom, inpTo].forEach(function(el) {
                    el.addEventListener('change', refresh);
                    el.addEventListener('input', refresh);
                });

                // Date preset buttons
                document.querySelectorAll('.preset-btn').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.preset-btn').forEach(function(b) {
                            b.className = b.className.replace('btn-success',
                                'btn-outline-secondary');
                        });
                        this.className = this.className.replace('btn-outline-secondary', 'btn-success');

                        var preset = this.dataset.preset;
                        var today = new Date();

                        function fmt(d) {
                            return d.getFullYear() +
                                '-' + String(d.getMonth() + 1).padStart(2, '0') +
                                '-' + String(d.getDate()).padStart(2, '0');
                        }

                        if (preset === 'all') {
                            inpFrom.value = '';
                            inpTo.value = '';
                        } else if (preset === 'today') {
                            inpFrom.value = fmt(today);
                            inpTo.value = fmt(today);
                        } else {
                            var from = new Date(today);
                            from.setDate(today.getDate() - parseInt(preset, 10));
                            inpFrom.value = fmt(from);
                            inpTo.value = fmt(today);
                        }

                        refresh();
                    });
                });

                // Refresh on modal open
                document.getElementById('exportModal')
                    .addEventListener('show.bs.modal', refresh);
            })();
        </script>
    @endpush

@endsection
