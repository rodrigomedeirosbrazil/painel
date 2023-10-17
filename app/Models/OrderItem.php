<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderItem extends Pivot
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'price',
        'price_repo',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_repo' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
