<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id',
    'sku',
    'name',
    'description',
    'unit_price',
    'reorder_level',
    'quantity_on_hand',
    'is_active',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'reorder_level' => 'integer',
            'quantity_on_hand' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<StockMovement, $this>
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->is_active && $this->quantity_on_hand <= $this->reorder_level;
    }

    public function recalculateOnHand(): void
    {
        $total = (int) $this->stockMovements()
            ->get(['type', 'quantity'])
            ->sum(fn (StockMovement $movement): int => $movement->signedQuantity());

        $this->forceFill(['quantity_on_hand' => $total])->saveQuietly();
    }

    /**
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->whereColumn('quantity_on_hand', '<=', 'reorder_level');
    }
}
