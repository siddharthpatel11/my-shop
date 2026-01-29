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
}
