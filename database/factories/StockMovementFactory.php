<?php

namespace Database\Factories;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => null,
            'type' => StockMovementType::Inbound,
            'quantity' => fake()->numberBetween(1, 12),
            'note' => fake()->optional()->sentence(),
            'occurred_at' => now(),
        ];
    }

    public function inbound(int $quantity = 5): static
    {
        return $this->state(fn (): array => [
            'type' => StockMovementType::Inbound,
            'quantity' => $quantity,
        ]);
    }

    public function outbound(int $quantity = 1): static
    {
        return $this->state(fn (): array => [
            'type' => StockMovementType::Outbound,
            'quantity' => $quantity,
        ]);
    }

    public function adjustment(int $quantity): static
    {
        return $this->state(fn (): array => [
            'type' => StockMovementType::Adjustment,
            'quantity' => $quantity,
        ]);
    }
}
