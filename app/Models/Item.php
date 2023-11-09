<?php

namespace App\Models;

use Carbon\CarbonInterface;
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

    public static function getAvailableStock(
        string $itemId,
        CarbonInterface $pickup,
        CarbonInterface $delivery,
        string $exceptOrderId = null
    ): int {
        $item = self::find($itemId);
        if (! $item) {
            return 0;
        }

        $quantityOrdered = OrderItem::query()
            ->where('item_id', $itemId)
            ->whereHas(
                'order',
                fn ($query) => $query->where('pickup', '>', $pickup)
                    ->where('delivery', '<', $delivery)
            )
            ->when($exceptOrderId, fn ($query) => $query->where('order_id', '!=', $exceptOrderId))
            ->sum('quantity');

        return $item->stock - $quantityOrdered;
    }
}
