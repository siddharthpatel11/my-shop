<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link',
        'background_color',
        'text_color',
        'order',
        'status',
    ];
}
