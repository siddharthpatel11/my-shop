<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Update</title>
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
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
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

        .order-number {
            font-size: 24px;
            font-weight: bold;
            color: #ff9800;
            margin-bottom: 10px;
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
            background: #ff9800;
            color: white;
            padding: 12px;
            text-align: left;
        }

        .products-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-danger {
            background: #dc3545;
            color: white;
        }

        .badge-secondary {
            background: #6c757d;
            color: white;
        }

        .summary-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
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
        <h1 style="margin: 0;">📦 Order Update</h1>
        <p style="margin: 10px 0 0 0;">Order {{ $order->order_number }}</p>
    </div>

    <div class="content">
        <p>Dear <strong>{{ $order->customer->name }}</strong>,</p>

        @php
            $totalItems = $order->activeItems()->count();
            $deliveredItems = $order->deliveredItems()->count();
            $availableItems = $order->availableItems()->count();
            $outOfStockItems = $order->outOfStockItems()->count();
            $deliverableCount = $deliveredItems + $availableItems;
            $allOutOfStock = $outOfStockItems === $totalItems;
        @endphp

        {{-- Alert box changes wording based on whether anything is deliverable --}}
        <div class="alert-box">
            <strong>⚠️ Important Update</strong><br>
            @if ($allOutOfStock)
                Unfortunately, all item(s) in your order are currently unavailable. We will keep you updated on when
                they become available or offer alternative options.
            @else
                Some items in your order are currently unavailable. We are delivering the available items and will
                process the remaining items separately.
            @endif
        </div>

        <div class="summary-box">
            <div class="order-number">Order #{{ $order->order_number }}</div>

            <p style="font-size: 16px; margin: 15px 0;">
                <strong>Delivery Summary:</strong><br>
                @if ($deliverableCount > 0)
                    ✅ {{ $deliverableCount }} of {{ $totalItems }} item(s) will be delivered<br>
                @endif
                ❌ {{ $outOfStockItems }} item(s) currently out of stock
            </p>
        </div>

        <h3>📦 Order Items Status</h3>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->activeItems as $item)
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
                        <td>
                            @if ($item->item_status == 'available' || $item->item_status == 'delivered')
                                <span class="badge badge-success">✓ Available</span>
                            @elseif($item->item_status == 'out_of_stock')
                                <span class="badge badge-danger">✗ Out of Stock</span>
                            @else
                                <span class="badge badge-secondary">Pending</span>
                            @endif
                            @if ($item->notes)
                                <br><small style="color: #666; font-style: italic;">{{ $item->notes }}</small>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Green box: only render when there actually are deliverable items --}}
        @if ($deliverableCount > 0)
            <div
                style="background: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <strong>✅ Items Being Delivered:</strong><br>
                @foreach ($order->activeItems as $item)
                    @if ($item->item_status == 'available' || $item->item_status == 'delivered')
                        • {{ $item->product->name }}
                        @if ($item->color)
                            ({{ $item->color->name }})
                        @endif
                        @if ($item->size)
                            - {{ $item->size->code ?? $item->size->name }}
                        @endif
                        - Qty: {{ $item->quantity }}<br>
                    @endif
                @endforeach
            </div>
        @endif

        {{-- Red box: out of stock items --}}
        @if ($outOfStockItems > 0)
            <div
                style="background: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <strong>❌ Items Currently Unavailable:</strong><br>
                @foreach ($order->activeItems as $item)
                    @if ($item->item_status == 'out_of_stock')
                        • {{ $item->product->name }}
                        @if ($item->color)
                            ({{ $item->color->name }})
                        @endif
                        @if ($item->size)
                            - {{ $item->size->code ?? $item->size->name }}
                        @endif
                        - Qty: {{ $item->quantity }}<br>
                        @if ($item->notes)
                            <small style="color: #666; padding-left: 15px;">Note: {{ $item->notes }}</small><br>
                        @endif
                    @endif
                @endforeach
                <br>
                <small><strong>What happens next?</strong> We will notify you once these items become available or offer
                    alternative options.</small>
            </div>
        @endif

        <div
            style="background: #cce5ff; border-left: 4px solid #004085; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <strong>📋 Next Steps:</strong><br>
            @if ($allOutOfStock)
                1. We are checking availability with our suppliers<br>
                2. We will contact you once items are back in stock<br>
                3. You can choose to wait for restock or request a full refund<br>
                4. Our customer support team will reach out to you soon
            @else
                1. You will receive the available items as scheduled<br>
                2. We will contact you regarding the unavailable items<br>
                3. You can choose to wait for restock or request a refund for unavailable items<br>
                4. Our customer support team will reach out to you soon
            @endif
        </div>

        @if ($order->payment_method == 'cod')
            <div
                style="background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 20px 0;">
                <strong>💵 Payment Information:</strong><br>
                @if ($allOutOfStock)
                    No payment is required at this time as all items are currently unavailable.
                @else
                    You will only pay for the items being delivered.<br>
                    @php
                        $deliveredTotal = $order->activeItems
                            ->whereIn('item_status', ['available', 'delivered'])
                            ->sum('subtotal');
                    @endphp
                    <strong>Amount to pay on delivery: ₹{{ number_format($deliveredTotal, 2) }}</strong>
                @endif
            </div>
        @endif

        <div
            style="background: #e7f3ff; padding: 15px; border-radius: 8px; border-left: 4px solid #0056b3; margin: 20px 0;">
            <strong>📞 Need Help?</strong><br>
            If you have any questions or concerns, please contact our customer support:<br>
            Email: support@myshop.com<br>
            Phone: 1800-XXX-XXXX
        </div>

        <p>We apologize for any inconvenience and appreciate your patience and understanding.</p>
    </div>

    <div class="footer">
        <p>Thank you for your understanding!</p>
        <p style="color: #999; font-size: 12px;">
            This is an automated email. Please do not reply to this message.
        </p>
        <p style="color: #999; font-size: 12px;">
            © {{ date('Y') }} MyShop. All rights reserved.
        </p>
    </div>

</body>

</html>
