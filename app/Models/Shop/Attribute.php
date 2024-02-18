<?php

namespace App\Models\Shop;

use App\Models\Shop\AttributeValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attribute extends Model
{
    use HasFactory;

    protected $table ='shop_attributes';
    protected $fillable = [
        'name',
        'is_visible'
    ];

    protected $casts = [
        'is_visible' => 'boolean'
    ];

    /**
     * Get all of the items for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'shop_attribute_id');
    }
}
