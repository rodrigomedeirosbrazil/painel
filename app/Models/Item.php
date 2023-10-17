<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'description',
        'value',
        'value_repo',
        'stock',
        'width',
        'height',
        'length',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'value_repo' => 'decimal:2',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
