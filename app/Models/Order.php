<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'customer_orders';

    protected $fillable = [
        'order_number',
        'customer_id',
        'address_id',
        'subtotal',
        'discount',
        'discount_code',
        'tax_id',
        'tax_amount',
        'shipping',
        'total',
        'order_status',
        'payment_status',
        'payment_method',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'notes',
        'status',
        'partial_delivery_notified',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
        'partial_delivery_notified' => 'boolean',
    ];

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber()
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(uniqid());
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Get the customer that owns the order
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the address for the order
     */
    public function address()
    {
        return $this->belongsTo(CustomerAddress::class, 'address_id');
    }

    /**
     * Get the tax applied to the order
     */
    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    /**
     * Get the order items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get only active order items
     */
    public function activeItems()
    {
        return $this->hasMany(OrderItem::class)->where('status', 'active');
    }

    /**
     * Get available items (items that can be delivered)
     */
    public function availableItems()
    {
        return $this->hasMany(OrderItem::class)
            ->where('status', 'active')
            ->where('item_status', 'available');
    }

    /**
     * Get out of stock items
     */
    public function outOfStockItems()
    {
        return $this->hasMany(OrderItem::class)
            ->where('status', 'active')
            ->where('item_status', 'out_of_stock');
    }

    /**
     * Get delivered items
     */
    public function deliveredItems()
    {
        return $this->hasMany(OrderItem::class)
            ->where('status', 'active')
            ->where('item_status', 'delivered');
    }

    /**
     * Get the discount applied
     */
    public function appliedDiscount()
    {
        if ($this->discount_code) {
            return Discount::where('code', $this->discount_code)->first();
        }
        return null;
    }

    /**
     * Get tax percentage from related tax record
     */
    public function getTaxPercentageAttribute()
    {
        return $this->tax ? $this->tax->rate : 0;
    }

    /**
     * Check if order has any out of stock or cancelled items.
     * Triggers on single-item orders too — any out_of_stock item = notify.
     */
    public function hasOutOfStockItems()
    {
        return $this->activeItems()
            ->whereIn('item_status', ['out_of_stock', 'cancelled'])
            ->exists();
    }

    /**
     * Alias kept so the Blade template ($order->hasPartialDelivery())
     * and the alert banner continue to work without changes.
     */
    public function hasPartialDelivery()
    {
        return $this->hasOutOfStockItems();
    }

    /**
     * Get delivery status message
     */
    public function getDeliveryStatusMessage()
    {
        $totalItems = $this->activeItems()->count();
        $deliveredItems = $this->deliveredItems()->count();
        $availableItems = $this->availableItems()->count();
        $outOfStockItems = $this->outOfStockItems()->count();

        if ($deliveredItems == $totalItems) {
            return 'All items delivered';
        } elseif (($deliveredItems + $availableItems) > 0 && $outOfStockItems > 0) {
            return ($deliveredItems + $availableItems) . " of {$totalItems} items available for delivery. {$outOfStockItems} item(s) out of stock.";
        } elseif ($outOfStockItems == $totalItems) {
            return "All {$totalItems} item(s) are currently out of stock.";
        } elseif ($outOfStockItems > 0) {
            return "{$outOfStockItems} of {$totalItems} items out of stock";
        } else {
            return 'Order pending';
        }
    }

    /**
     * Scope for filtering by order_status
     */
    public function scopeOrderStatus($query, $orderStatus)
    {
        return $query->where('order_status', $orderStatus);
    }

    /**
     * Scope for filtering by payment status
     */
    public function scopePaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Scope for active records only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get formatted order total
     */
    public function getFormattedTotalAttribute()
    {
        return '₹' . number_format($this->total, 2);
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute()
    {
        return '₹' . number_format($this->subtotal, 2);
    }

    /**
     * Get formatted discount
     */
    public function getFormattedDiscountAttribute()
    {
        return '₹' . number_format($this->discount, 2);
    }

    /**
     * Get formatted tax
     */
    public function getFormattedTaxAttribute()
    {
        return '₹' . number_format($this->tax_amount, 2);
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->order_status, ['pending', 'processing']);
    }

    /**
     * Get order status badge color
     */
    public function getOrderStatusBadgeColorAttribute()
    {
        return match ($this->order_status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get payment status badge color
     */
    public function getPaymentStatusBadgeColorAttribute()
    {
        return match ($this->payment_status) {
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get payment method name
     */
    public function getPaymentMethodNameAttribute()
    {
        return match ($this->payment_method) {
            'cod' => 'Cash on Delivery',
            'cash' => 'Cash',
            'upi' => 'UPI',
            'razorpay' => 'Razorpay',
            'razorpay_upi' => 'Razorpay UPI',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'paypal' => 'PayPal',
            'stripe' => 'Stripe',
            'bank_transfer' => 'Bank Transfer',
            default => 'N/A',
        };
    }

    /**
     * Get payment method icon
     */
    public function getPaymentMethodIconAttribute()
    {
        return match ($this->payment_method) {
            'cod' => 'cash-stack',
            'cash' => 'cash',
            'upi' => 'lightning-fill',
            'razorpay' => 'credit-card',
            'razorpay_upi' => 'lightning-charge-fill',
            'credit_card' => 'credit-card-2-front',
            'debit_card' => 'credit-card-2-back',
            'paypal' => 'paypal',
            'stripe' => 'stripe',
            'bank_transfer' => 'bank',
            default => 'question-circle',
        };
    }
}
