<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    public $timestamps = true;
    protected $table = "products";

    protected $fillable = [
        'name',
        'detail',
        'image',
        'category_id',
        'size_id',
        'color_id',
        'price',
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
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sizeIds(): array
    {
        return $this->size_id ? explode(',', $this->size_id) : [];
    }

    public function colorIds(): array
    {
        return $this->color_id ? explode(',', $this->color_id) : [];
    }
    // public function size()
    // {
    //     return $this->belongsTo(Size::class);
    // }
    // public function color()
    // {
    //     return $this->belongsTo(Color::class);
    // }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
}
