<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Item extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'value',
        'value_repo',
        'quantity',
        'width',
        'height',
        'length',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'value_repo' => 'decimal:2',
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_item', 'item_id', 'order_id');
    }
}
