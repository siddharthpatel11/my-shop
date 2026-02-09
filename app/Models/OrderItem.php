<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'customer_order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'color_id',
        'size_id',
        'quantity',
        'price',
        'subtotal',
        'item_status',
        'status',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Get the order that owns the item
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the color
     */
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * Get the size
     */
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * Scope for active items only
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for available items
     */
    public function scopeAvailable($query)
    {
        return $query->where('item_status', 'available');
    }

    /**
     * Scope for out of stock items
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('item_status', 'out_of_stock');
    }

    /**
     * Scope for delivered items
     */
    public function scopeDelivered($query)
    {
        return $query->where('item_status', 'delivered');
    }


    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return '₹' . number_format($this->price, 2);
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute()
    {
        return '₹' . number_format($this->subtotal, 2);
    }
    public function getItemStatusBadgeColorAttribute()
    {
        return match ($this->item_status) {
            'pending' => 'secondary',
            'available' => 'success',
            'out_of_stock' => 'danger',
            'delivered' => 'primary',
            'cancelled' => 'dark',
            default => 'secondary',
        };
    }

    /**
     * Get item status label
     */
    public function getItemStatusLabelAttribute()
    {
        return match ($this->item_status) {
            'pending' => 'Pending',
            'available' => 'Available',
            'out_of_stock' => 'Out of Stock',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }
}
