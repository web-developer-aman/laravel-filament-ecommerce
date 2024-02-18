<?php

namespace App\Models\Shop;

use App\Models\Shop\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttributeValue extends Model
{
    use HasFactory;

    protected $table ='shop_attribute_values';

    protected $fillable = [
        'shop_attributes_id',
        'value'
    ];

    /**
     * Get the user that owns the AttributeValue
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attributes(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'shop_attribute_id');
    }


}
