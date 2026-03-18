<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaTag extends Model
{
    protected $table = 'meta_tags';
    protected $fillable = [
        'page_identifier',
        'seo_title',
        'seo_description',
        'seo_key',
        'seo_canonical',
        'seo_image',
        'og_title',
        'og_description',
        'og_image',
        'og_key',
    ];
}
