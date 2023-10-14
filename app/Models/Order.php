<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'description',
        'customer_id',
        'pickup',
        'delivery',
        'deposit',
        'discount',
        'amount',
    ];

    protected $casts = [
        'pickup' => 'date',
        'delivery' => 'date',
        'deposit' => 'decimal:2',
        'discount' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'order_item', 'order_id', 'item_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
