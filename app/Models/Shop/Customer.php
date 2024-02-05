<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'shop_customers';

    protected $casts = [
        'birthday' => 'date'
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'birthday',
        'photo',
        'gender',

    ];
}
