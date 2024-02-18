<?php

namespace App\Models\Shop;

use App\Models\Shop\Product;
use Spatie\MediaLibrary\HasMedia;
use App\Models\Shop\AttributeValue;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariation extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table ='shop_product_variations';

    protected $fillable = [
        'name', 'type', 'order','sku', 'price', 'parent_id'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function($variation){
            $variation->order = ProductVariation::where('shop_product_id', $variation->shop_product_id)->max('order') + 1;
        });
    }

    public function parent(): BelongsTo{
        return $this->belongsTo(ProductVariation::class, 'parent_id');
    }

    /**
     * The roles that belong to the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, 'product_variation_attribute_value', 'shop_product_variation_id', 'shop_attribute_value_id')->withTimestamps();
    }

    /**
     * Get the user that owns the ProductVariation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'shop_product_id');
    }
}
