<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(\App\Observers\TranslationObserver::class)]
class Product extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = true;
    protected $table = "products";

    protected $fillable = [
        'name',
        'name_gu',
        'name_hi',
        'name_sa',
        'name_bn',
        'detail',
        'detail_gu',
        'detail_hi',
        'detail_bn',
        'detail_sa',
        'image',
        'category_id',
        'size_id',
        'color_id',
        'price',
        'stock',
        'status',
        'seo_meta_title',
        'seo_meta_description',
        'seo_meta_key',
        'seo_meta_image',
        'seo_canonical',
        'og_meta_title',
        'og_meta_description',
        'og_meta_key',
        'og_meta_image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Check if product has enough stock
     */
    public function hasStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Decrement stock
     */
    public function decrementStock(int $quantity)
    {
        $this->decrement('stock', $quantity);
    }

    /**
     * Increment stock
     */
    public function incrementStock(int $quantity)
    {
        $this->increment('stock', $quantity);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getNameAttribute($value)
    {
        $locale = app()->getLocale();
        if ($locale === 'gu' && !empty($this->attributes['name_gu'])) {
            return $this->attributes['name_gu'];
        }
        if ($locale === 'hi' && !empty($this->attributes['name_hi'])) {
            return $this->attributes['name_hi'];
        }
        if ($locale === 'sa' && !empty($this->attributes['name_sa'])) {
            return $this->attributes['name_sa'];
        }
        if ($locale === 'bn' && !empty($this->attributes['name_bn'])) {
            return $this->attributes['name_bn'];
        }
        return $value;
    }

    public function getDetailAttribute($value)
    {
        $locale = app()->getLocale();
        if ($locale === 'gu' && !empty($this->attributes['detail_gu'])) {
            return $this->attributes['detail_gu'];
        }
        if ($locale === 'hi' && !empty($this->attributes['detail_hi'])) {
            return $this->attributes['detail_hi'];
        }
        if ($locale === 'sa' && !empty($this->attributes['detail_sa'])) {
            return $this->attributes['detail_sa'];
        }
        if ($locale === 'bn' && !empty($this->attributes['detail_bn'])) {
            return $this->attributes['detail_bn'];
        }
        return $value;
    }

    public function sizeIds(): array
    {
        return $this->size_id ? explode(',', $this->size_id) : [];
    }

    public function colorIds(): array
    {
        return $this->color_id ? explode(',', $this->color_id) : [];
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
