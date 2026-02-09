<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        .order-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .order-number {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
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

        .products-table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            margin: 20px 0;
            border-radius: 8px;
            overflow: hidden;
        }

        .products-table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .products-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .address-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
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
            color: #667eea;
            border-top: 2px solid #667eea;
            padding-top: 15px;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }

        .success-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin: 10px 0;
        }

        .discount-badge {
            background: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 style="margin: 0;">🎉 Order Confirmed!</h1>
        <p style="margin: 10px 0 0 0;">Thank you for your order</p>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $order->customer->name }}</strong>,</p>

        <p>Thank you for shopping with us! Your order has been successfully placed and is being processed.</p>

        <div class="order-details">
            <div class="order-number">Order #{{ $order->order_number }}</div>
            <span class="success-badge">{{ ucfirst($order->status) }}</span>

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
                    <span class="info-label">Payment Status:</span>
                    <span>{{ ucfirst($order->payment_status) }}</span>
                </div>
            </div>
        </div>

        <h3>📦 Order Items</h3>
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
                            <span class="discount-badge">{{ $order->discount_code }}</span>
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

        <h3>📍 Delivery Address</h3>
        <div class="address-box">
            <strong>{{ $order->customer->name }}</strong><br>
            @if ($order->address->full_address)
                {{ $order->address->full_address }}<br>
            @endif
            {{ $order->address->city }}, {{ $order->address->district }}<br>
            {{ $order->address->state }}, {{ $order->address->country }}
            @if ($order->address->pincode)
                - {{ $order->address->pincode }}
            @endif
        </div>

        @if ($order->payment_method == 'cod')
            <div
                style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 20px 0;">
                <strong>💵 Cash on Delivery</strong><br>
                Please keep <strong>₹{{ number_format($order->total, 2) }}</strong> ready in cash when you receive your
                order.
            </div>
        @endif

        <div
            style="background: #d1ecf1; padding: 15px; border-radius: 8px; border-left: 4px solid #17a2b8; margin: 20px 0;">
            <strong>📧 Need Help?</strong><br>
            If you have any questions about your order, please contact our customer support.
        </div>
    </div>

    <div class="footer">
        <p>Thank you for shopping with us!</p>
        <p style="color: #999; font-size: 12px;">
            This is an automated email. Please do not reply to this message.
        </p>
        <p style="margin: 20px 0;">
            <a href="#" style="color: #667eea; text-decoration: none; margin: 0 10px;">Facebook</a>
            <a href="#" style="color: #667eea; text-decoration: none; margin: 0 10px;">Twitter</a>
            <a href="#" style="color: #667eea; text-decoration: none; margin: 0 10px;">Instagram</a>
        </p>
        <p style="color: #999; font-size: 12px;">
            © {{ date('Y') }} MyShop. All rights reserved.
        </p>
    </div>
</body>

</html>
