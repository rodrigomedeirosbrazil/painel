<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    protected $fillable = [
        'quantity',
        'price',
        'price_repo',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_repo' => 'decimal:2',
    ];
}
