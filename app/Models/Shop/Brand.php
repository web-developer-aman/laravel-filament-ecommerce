<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'shop_brands';

    protected $casts = [
        'is_visible' => 'boolean'
    ];

    protected $fillable = ['name', 'slug', 'description', 'is_visible', 'seo_title', 'seo_description'];
}
