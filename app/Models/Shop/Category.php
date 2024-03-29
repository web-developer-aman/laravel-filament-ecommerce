<?php

namespace App\Models\Shop;

use App\Models\Shop\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $table = 'shop_categories';

    protected $casts = [
        'is_visible' => 'boolean'
    ];

    protected $fillable = ['name', 'slug', 'parent_id', 'is_visible', 'description', 'seo_title', 'seo_description'];

    public function children(): HasMany{
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent(): BelongsTo{
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'shop_category_product', 'shop_category_id', 'shop_product_id');
    }

    

}
