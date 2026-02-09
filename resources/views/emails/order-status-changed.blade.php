<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Update</title>
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

        .status-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }

        .status-change {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin: 20px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 16px;
        }

        .status-pending {
            background: #ffc107;
            color: #000;
        }

        .status-processing {
            background: #17a2b8;
            color: white;
        }

        .status-shipped {
            background: #007bff;
            color: white;
        }

        .status-delivered {
            background: #28a745;
            color: white;
        }

        .status-cancelled {
            background: #dc3545;
            color: white;
        }

        .arrow {
            font-size: 30px;
            color: #667eea;
        }

        .order-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 style="margin: 0;">📦 Order Status Updated!</h1>
        <p style="margin: 10px 0 0 0;">Your order has been updated</p>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $order->customer->name }}</strong>,</p>

        <p>Great news! Your order status has been updated.</p>

        <div class="status-box">
            <h3 style="margin-top: 0; color: #667eea;">Order #{{ $order->order_number }}</h3>

            <div class="status-change">
                <span class="status-badge status-{{ $oldStatus }}">
                    {{ ucfirst($oldStatus) }}
                </span>
                <span class="arrow">→</span>
                <span class="status-badge status-{{ $newStatus }}">
                    {{ ucfirst($newStatus) }}
                </span>
            </div>

            @if ($newStatus == 'processing')
                <p style="margin-top: 20px;">
                    🎉 Your order is now being prepared! We're getting your items ready for shipment.
                </p>
            @elseif($newStatus == 'shipped')
                <p style="margin-top: 20px;">
                    🚚 Your order has been shipped! It's on its way to you.
                </p>
            @elseif($newStatus == 'delivered')
                <p style="margin-top: 20px;">
                    ✅ Your order has been delivered! We hope you enjoy your purchase.
                </p>
            @elseif($newStatus == 'cancelled')
                <p style="margin-top: 20px;">
                    ❌ Your order has been cancelled. If you have any questions, please contact our support team.
                </p>
            @endif
        </div>

        <div class="order-details">
            <h3 style="margin-top: 0;">📋 Order Details</h3>

            <div class="info-row">
                <span><strong>Order Number:</strong></span>
                <span>{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span><strong>Order Date:</strong></span>
                <span>{{ $order->created_at->format('d M, Y h:i A') }}</span>
            </div>
            <div class="info-row">
                <span><strong>Total Amount:</strong></span>
                <span><strong style="color: #667eea;">₹{{ number_format($order->total, 2) }}</strong></span>
            </div>
            <div class="info-row">
                <span><strong>Payment Method:</strong></span>
                <span>{{ strtoupper($order->payment_method) }}</span>
            </div>
            <div class="info-row">
                <span><strong>Payment Status:</strong></span>
                <span>
                    @if ($order->payment_status == 'paid')
                        <span style="color: #28a745; font-weight: bold;">✓ Paid</span>
                    @elseif($order->payment_status == 'pending')
                        <span style="color: #ffc107; font-weight: bold;">⏳ Pending</span>
                    @else
                        <span style="color: #dc3545; font-weight: bold;">✗ Failed</span>
                    @endif
                </span>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h4 style="margin-top: 0;">📦 Items in Your Order ({{ $order->items->count() }})</h4>
            @foreach ($order->items as $item)
                <div style="padding: 10px 0; border-bottom: 1px solid #eee;">
                    <strong>{{ $item->product->name }}</strong>
                    @if ($item->color)
                        <span style="color: #666;"> - Color: {{ $item->color->name }}</span>
                    @endif
                    @if ($item->size)
                        <span style="color: #666;"> - Size: {{ $item->size->code ?? $item->size->name }}</span>
                    @endif
                    <br>
                    <small style="color: #666;">Quantity: {{ $item->quantity }} ×
                        ₹{{ number_format($item->price, 2) }} = ₹{{ number_format($item->subtotal, 2) }}</small>
                </div>
            @endforeach
        </div>

        @if ($order->address)
            <div
                style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea;">
                <h4 style="margin-top: 0;">📍 Delivery Address</h4>
                <p style="margin: 0;">
                    {{ $order->customer->name }}<br>
                    @if ($order->address->full_address)
                        {{ $order->address->full_address }}<br>
                    @endif
                    {{ $order->address->city }}, {{ $order->address->district }}<br>
                    {{ $order->address->state }}, {{ $order->address->country }}
                    @if ($order->address->pincode)
                        - {{ $order->address->pincode }}
                    @endif
                </p>
            </div>
        @endif

        @if ($order->payment_method == 'cod' && $order->payment_status == 'pending')
            <div
                style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 20px 0;">
                <strong>💵 Payment Information:</strong><br>
                Please keep <strong>₹{{ number_format($order->total, 2) }}</strong> ready in cash when you receive your
                order.
            </div>
        @endif

        <div
            style="background: #e7f3ff; padding: 15px; border-radius: 8px; border-left: 4px solid #0056b3; margin: 20px 0;">
            <strong>📞 Need Help?</strong><br>
            If you have any questions about your order, please contact our customer support.
        </div>
    </div>

    <div class="footer">
        <p>Thank you for shopping with us!</p>
        <p style="color: #999; font-size: 12px;">
            This is an automated email. Please do not reply to this message.
        </p>
        <p style="color: #999; font-size: 12px;">
            © {{ date('Y') }} MyShop. All rights reserved.
        </p>
    </div>
</body>

</html>
