<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{

    use HasFactory;

     protected $table = 'cart_items';


    protected $fillable = [
        'customer_id',
        'product_id',
        'color_id',
        'size_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the cart item
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the product for this cart item
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the color for this cart item
     */
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * Get the size for this cart item
     */
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * Get the subtotal for this cart item
     */
    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }
}
