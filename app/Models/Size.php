<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Size extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "sizes";
    protected $fillable = [
        'name',
        // 'code',
        'status'
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'size', 'name');
    }
}
