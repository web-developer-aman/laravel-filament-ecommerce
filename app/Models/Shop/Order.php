<?php

namespace App\Models\Shop;

use App\Enums\OrderStatus;
use App\Models\Shop\Customer;
use App\Models\Shop\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $table ='shop_orders';

    protected $fillable = [
        'number',
        'total_price',
        'status',
        'currency',
        'notes',
        'shipping_price',
        'shipping_method'
    ];

    protected $casts = [
        'status' => OrderStatus::class
    ];

    /**
     * Get the customer that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'shop_customer_id');
    }

    /**
     * Get all of the items for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'shop_order_id');
    }
}
