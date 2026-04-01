<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(\App\Observers\TranslationObserver::class)]
class Color extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'colors';


    protected $fillable = [
        'name',
        'name_gu',
        'name_hi',
        'name_sa',
        'name_bn',
        'hex_code',
        'status'
    ];

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

    public function products()
    {
        return $this->hasMany(Product::class, 'color', 'name');
    }
}
