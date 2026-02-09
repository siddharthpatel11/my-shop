<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Received</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            background: #f8f9fa;
            padding: 30px;
            border: 1px solid #dee2e6;
        }

        .alert-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .order-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .order-number {
            font-size: 24px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .info-label {
            font-weight: bold;
            color: #666;
        }

        .customer-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }

        .products-table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            margin: 20px 0;
            border-radius: 8px;
            overflow: hidden;
        }

        .products-table th {
            background: #dc3545;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .products-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .total-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .total-row.grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #dc3545;
            border-top: 2px solid #dc3545;
            padding-top: 15px;
            margin-top: 10px;
        }

        .action-button {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-warning {
            background: #ffc107;
            color: #333;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 style="margin: 0;">🔔 New Order Alert!</h1>
        <p style="margin: 10px 0 0 0;">A customer has placed a new order</p>
    </div>

    <div class="content">
        <div class="alert-box">
            <strong>⚠️ Action Required!</strong><br>
            A new order has been received and requires your attention.
        </div>

        <div class="order-details">
            <div class="order-number">Order #{{ $order->order_number }}</div>
            <span class="badge badge-warning">{{ ucfirst($order->status) }}</span>
            <span class="badge badge-info">{{ ucfirst($order->payment_status) }}</span>

            <div style="margin-top: 20px;">
                <div class="info-row">
                    <span class="info-label">Order Date:</span>
                    <span>{{ $order->created_at->format('d M, Y h:i A') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Method:</span>
                    <span>{{ strtoupper($order->payment_method) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Items:</span>
                    <span>{{ $order->items->count() }} item(s)</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Order Total:</span>
                    <span style="font-weight: bold; color: #dc3545;">₹{{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <h3>👤 Customer Information</h3>
        <div class="customer-box">
            <strong>{{ $order->customer->name }}</strong><br>
            <strong>Email:</strong> {{ $order->customer->email }}<br>
            @if ($order->customer->phone)
                <strong>Phone:</strong> {{ $order->customer->phone }}<br>
            @endif
            <br>
            <strong>📍 Delivery Address:</strong><br>
            @if ($order->address->full_address)
                {{ $order->address->full_address }}<br>
            @endif
            {{ $order->address->city }}, {{ $order->address->district }}<br>
            {{ $order->address->state }}, {{ $order->address->country }}
            @if ($order->address->pincode)
                - {{ $order->address->pincode }}
            @endif
        </div>

        <h3>📦 Ordered Products</h3>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product->name }}</strong>
                            @if ($item->color)
                                <br><small style="color: #666;">Color: {{ $item->color->name }}</small>
                            @endif
                            @if ($item->size)
                                <br><small style="color: #666;">Size:
                                    {{ $item->size->code ?? $item->size->name }}</small>
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹{{ number_format($item->price, 2) }}</td>
                        <td><strong>₹{{ number_format($item->subtotal, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <h3 style="margin-top: 0;">💰 Order Summary</h3>
            <div class="total-row">
                <span>Subtotal:</span>
                <span>₹{{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if ($order->tax_amount > 0)
                <div class="total-row">
                    <span>Tax ({{ $order->tax->name ?? 'GST' }}):</span>
                    <span>₹{{ number_format($order->tax_amount, 2) }}</span>
                </div>
            @endif
            <div class="total-row">
                <span>Shipping:</span>
                <span style="color: #28a745; font-weight: bold;">FREE</span>
            </div>
            @if ($order->discount > 0)
                <div class="total-row" style="color: #28a745;">
                    <span>
                        Discount
                        @if ($order->discount_code)
                            ({{ $order->discount_code }})
                        @endif
                    </span>
                    <span><strong>- ₹{{ number_format($order->discount, 2) }}</strong></span>
                </div>
            @endif
            <div class="total-row grand-total">
                <span>Total Amount:</span>
                <span>₹{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        @if ($order->payment_method == 'cod')
            <div
                style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 20px 0;">
                <strong>💵 Cash on Delivery</strong><br>
                Customer will pay <strong>₹{{ number_format($order->total, 2) }}</strong> in cash upon delivery.
            </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/admin/orders/' . $order->id) }}" class="action-button">
                View Order Details in Admin Panel
            </a>
        </div>

        <div
            style="background: #d1ecf1; padding: 15px; border-radius: 8px; border-left: 4px solid #17a2b8; margin: 20px 0;">
            <strong>📋 Next Steps:</strong><br>
            1. Review the order details<br>
            2. Confirm product availability<br>
            3. Update order status to "Processing"<br>
            4. Prepare items for shipping
        </div>
    </div>

    <div class="footer">
        <p style="color: #999; font-size: 12px;">
            This is an automated notification from your order management system.
        </p>
        <p style="color: #999; font-size: 12px;">
            © {{ date('Y') }} MyShop Admin Panel. All rights reserved.
        </p>
    </div>
</body>

</html>
