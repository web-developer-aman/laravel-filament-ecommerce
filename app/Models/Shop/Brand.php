<?php

namespace App\Models\Shop;

use App\Models\Shop\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'shop_brands';

    protected $casts = [
        'is_visible' => 'boolean'
    ];

    protected $fillable = ['name', 'slug', 'description', 'is_visible', 'seo_title', 'seo_description'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'shop_brand_id');
    }
}
