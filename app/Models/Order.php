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
        'status',
        'payment_status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
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
     * Scope for filtering by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by payment status
     */
    public function scopePaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
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
        return in_array($this->status, ['pending', 'processing']);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColorAttribute()
    {
        return match ($this->status) {
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
}
