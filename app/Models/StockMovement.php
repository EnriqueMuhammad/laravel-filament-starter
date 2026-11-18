<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\StockMovementType;
use Database\Factories\StockMovementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * @property StockMovementType $type
 * @property int $quantity
 * @property Carbon $occurred_at
 * @property int $product_id
 * @property int|null $user_id
 */
#[Fillable([
    'product_id',
    'user_id',
    'type',
    'quantity',
    'note',
    'occurred_at',
])]
class StockMovement extends Model
{
    /** @use HasFactory<StockMovementFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => StockMovementType::class,
            'quantity' => 'integer',
            'occurred_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (StockMovement $movement): void {
            $movement->occurred_at ??= now();
            $movement->user_id ??= auth()->id();

            if ($movement->type !== StockMovementType::Adjustment && $movement->quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity must be at least 1.',
                ]);
            }

            if ($movement->type === StockMovementType::Adjustment && $movement->quantity === 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'An adjustment cannot be zero.',
                ]);
            }

            $product = $movement->product()->first();

            if ($product === null) {
                return;
            }

            $existing = (int) $product->stockMovements()
                ->when($movement->exists, fn ($query) => $query->whereKeyNot($movement->getKey()))
                ->get(['type', 'quantity'])
                ->sum(fn (StockMovement $row): int => $row->signedQuantity());

            if ($existing + $movement->signedQuantity() < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'That movement would take stock below zero.',
                ]);
            }
        });

        static::saved(function (StockMovement $movement): void {
            $movement->product?->recalculateOnHand();

            if ($movement->wasChanged('product_id')) {
                $original = $movement->getOriginal('product_id');

                if (is_numeric($original)) {
                    Product::query()->find((int) $original)?->recalculateOnHand();
                }
            }
        });

        static::deleted(function (StockMovement $movement): void {
            $movement->product?->recalculateOnHand();
        });
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function signedQuantity(): int
    {
        return match ($this->type) {
            StockMovementType::Inbound => abs($this->quantity),
            StockMovementType::Outbound => -abs($this->quantity),
            StockMovementType::Adjustment => $this->quantity,
        };
    }
}
