@php
    $settings = \App\Models\LayoutSetting::getActive();
    $logoUrl = $settings->frontend_logo_url;
    $appName = $settings->frontend_app_name ?? config('app.name');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <!-- Modern Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }

        .invoice-wrapper {
            max-width: 850px;
            margin: 40px auto;
            padding: 50px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            position: relative;
            overflow: hidden;
        }

        .invoice-wrapper::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
        }

        .header-top {
            margin-bottom: 50px;
        }

        .invoice-label {
            font-size: 3rem;
            font-weight: 800;
            color: #f1f5f9;
            position: absolute;
            top: 20px;
            right: 40px;
            pointer-events: none;
            text-transform: uppercase;
        }

        .logo-box {
            max-height: 60px;
            margin-bottom: 20px;
        }

        .logo-box img {
            max-height: 60px;
            width: auto;
        }

        .logo-placeholder {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #2563eb;
        }

        .billing-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 50px;
        }

        .info-block h6 {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
        }

        .info-block p {
            margin-bottom: 4px;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .table-container {
            margin-bottom: 40px;
        }

        .table thead th {
            background: #f8fafc;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .table tbody td {
            padding: 24px 16px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .item-name {
            font-weight: 700;
            font-size: 1.05rem;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .item-meta {
            font-size: 0.8rem;
            color: #64748b;
        }

        .summary-box {
            margin-top: 40px;
            padding-top: 20px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 1rem;
            color: #475569;
            border-bottom: 1px solid #f1f5f9;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-item.total {
            margin-top: 10px;
            padding: 20px 0;
            border-top: 2px solid #e2e8f0;
            border-bottom: 2px solid #e2e8f0;
            color: #0f172a;
        }

        .total-label {
            font-size: 1.25rem;
            font-weight: 800;
            color: #2563eb;
            text-transform: capitalize;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: 800;
            color: #2563eb;
        }

        .footer-note {
            margin-top: 80px;
            padding-top: 30px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .invoice-wrapper {
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 20px;
                width: 100%;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="container no-print mt-4 text-center">
        <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
            <button onclick="downloadPDF(event)" class="btn btn-primary px-4 border-0">
                <i class="fas fa-download me-2"></i> Download PDF
            </button>
            <button onclick="window.print()" class="btn btn-dark px-4 border-0">
                <i class="fas fa-print me-2"></i> Print
            </button>
            <a href="{{ route('frontend.orders') }}" class="btn btn-white border-0 px-4">
                <i class="fas fa-times me-2"></i> Close
            </a>
        </div>
    </div>

    <div class="invoice-wrapper" id="invoice-content">
        <div class="invoice-label">Invoice</div>

        <!-- Header -->
        <div class="header-top">
            <div class="logo-box">
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $appName }}">
                @else
                    <div class="logo-placeholder">
                        <i class="fas fa-shopping-bag fa-2x"></i>
                        <h3 class="fw-bold mb-0">{{ $appName }}</h3>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-8">
                    <div class="text-muted small">
                        <p class="mb-0">{{ $settings->contact_address }}</p>
                        <p class="mb-0">Contact:
                            {{ is_array($settings->contact_phone) ? $settings->contact_phone[0] : $settings->contact_phone }}
                        </p>
                        <p class="mb-0">Email:
                            {{ is_array($settings->contact_email) ? $settings->contact_email[0] : $settings->contact_email }}
                        </p>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <h5 class="fw-bold mb-1">#{{ $order->order_number }}</h5>
                    <p class="text-muted small mb-0">Date: {{ $order->created_at->format('d M, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Billing Info -->
        <div class="billing-grid">
            <div class="info-block">
                <h6>Billed To</h6>
                <p class="fw-bold text-dark fs-5 mb-2">{{ $order->customer->name }}</p>
                <p class="text-muted">{{ $order->address->full_address }}</p>
                <p class="text-muted">{{ $order->address->city }}, {{ $order->address->district }}</p>
                <p class="text-muted">{{ $order->address->state }} - {{ $order->address->pincode }}</p>
            </div>
            <div class="info-block text-end">
                <h6>Order Details</h6>
                <p><strong>Payment:</strong> {{ $order->payment_method_name }}</p>
                <p><strong>Status:</strong> <span class="text-primary fw-bold">{{ strtoupper($order->status) }}</span>
                </p>
                @if ($order->razorpay_payment_id)
                    <p class="small text-muted">Trans. ID: {{ $order->razorpay_payment_id }}</p>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="table-container">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th class="ps-0">Item Description</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Rate</th>
                        <th class="text-end pe-0">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="ps-0">
                                <div class="item-name">{{ $item->product->name }}</div>
                                <div class="item-meta">
                                    {{ $item->product->category->name ?? 'General' }}
                                    @if ($item->color)
                                        <span class="mx-1">|</span> Color: {{ $item->color->name }}
                                    @endif
                                    @if ($item->size)
                                        <span class="mx-1">|</span> Size: {{ $item->size->name }}
                                    @endif
                                </div>
                            </td>
                            <td class="text-center fw-bold">{{ $item->quantity }}</td>
                            <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                            <td class="text-end pe-0 fw-bold">₹{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="summary-box">
            <div class="summary-item">
                <span class="text-muted">Subtotal</span>
                <span class="fw-bold text-dark">₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if ($order->shipping > 0)
                <div class="summary-item">
                    <span class="text-muted">Shipping</span>
                    <span class="fw-bold text-dark">₹{{ number_format($order->shipping, 2) }}</span>
                </div>
            @endif
            @if ($order->tax_amount > 0)
                <div class="summary-item">
                    <span class="text-muted">GST (Tax)</span>
                    <span class="fw-bold text-dark">₹{{ number_format($order->tax_amount, 2) }}</span>
                </div>
            @endif
            @if ($order->discount > 0)
                <div class="summary-item text-success">
                    <span class="fw-bold">Discount ({{ $order->discount_code }})</span>
                    <span class="fw-bold">-₹{{ number_format($order->discount, 2) }}</span>
                </div>
            @endif
            <div class="summary-item total">
                <span class="total-label">Total Amount</span>
                <span class="total-amount">₹{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-note">
            <p class="mb-1 fw-bold text-dark">Thank you for your business!</p>
            <p class="mb-0">This is a digital receipt generated on {{ date('d M, Y H:i') }}.</p>
        </div>

        <!-- html2pdf Library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
        <script>
            function downloadPDF(event) {
                const element = document.getElementById('invoice-content');
                const opt = {
                    margin: 0,
                    filename: 'Invoice-{{ $order->order_number }}.pdf',
                    image: {
                        type: 'jpeg',
                        quality: 0.98
                    },
                    html2canvas: {
                        scale: 2,
                        useCORS: true
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a4',
                        orientation: 'portrait'
                    }
                };

                const btn = event.currentTarget;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Generating...';
                btn.disabled = true;

                html2pdf().from(element).set(opt).save().then(() => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                });
            }
        </script>
</body>

</html>
