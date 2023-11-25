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

    public static function getItemAvailableStock(
        string | Model $item,
        string $pickup,
        string $delivery,
        string $exceptOrderId = null
    ): int {
        if (is_string($item)) {
            $item = self::find($item);
        }

        if (! $item) {
            return 0;
        }

        return $item->getAvailableStock($pickup, $delivery, $exceptOrderId);
    }

    public function getAvailableStock(
        string $pickup,
        string $delivery,
        string $exceptOrderId = null
    ): int {
        $quantityOrdered = OrderItem::query()
            ->where('item_id', $this->id)
            ->whereHas(
                'order',
                fn ($query) => $query->where('pickup', '<=', $delivery)
                    ->where('delivery', '>=', $pickup)
            )
            ->when($exceptOrderId, fn ($query) => $query->where('order_id', '!=', $exceptOrderId))
            ->sum('quantity');

        return $this->stock - $quantityOrdered;
    }
}
