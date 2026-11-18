<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\StockMovementType;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        if (Product::query()->exists()) {
            return;
        }

        $manager = User::query()->where('email', 'manager@example.com')->first();

        $catalog = [
            'Coffee' => [
                'description' => 'Roasted lots packed for the bar and for retail bags.',
                'products' => [
                    ['ARU-ESP-250', 'Espresso Blend 250g', 16.00, 10, 40, 28],
                    ['ARU-ETH-250', 'Yirgacheffe 250g', 18.50, 8, 24, 20],
                    ['ARU-SUM-1KG', 'Sumatra 1kg', 32.00, 6, 18, 6],
                ],
            ],
            'Tea' => [
                'description' => 'Loose leaf tins kept beside the brew bar.',
                'products' => [
                    ['ARU-JAS-100', 'Jasmine Pearls 100g', 14.00, 6, 15, 4],
                    ['ARU-EARL-80', 'Earl Grey 80g', 9.50, 8, 10, 7],
                ],
            ],
            'Equipment' => [
                'description' => 'Brewing tools that leave the shop with a bag of coffee.',
                'products' => [
                    ['ARU-V60-02', 'Hario V60 02', 12.00, 4, 20, 5],
                    ['ARU-KET-600', 'Stovetop Kettle', 28.00, 3, 8, 2],
                    ['ARU-SCL-01', 'Bar Scale', 42.00, 3, 6, 4],
                ],
            ],
            'Pantry' => [
                'description' => 'Staples used in the pastry case and sold by the jar.',
                'products' => [
                    ['ARU-COC-500', 'Coconut Sugar 500g', 7.50, 10, 30, 12],
                    ['ARU-HNY-340', 'Wild Honey 340g', 11.00, 6, 16, 5],
                ],
            ],
            'Merchandise' => [
                'description' => 'Goods that carry the Aruna mark out of the shop.',
                'products' => [
                    ['ARU-MUG-01', 'Stoneware Mug', 18.00, 6, 14, 9],
                    ['ARU-TOTE-01', 'Canvas Tote', 15.00, 4, 12, 3],
                ],
            ],
        ];

        foreach ($catalog as $categoryName => $categoryData) {
            $category = Category::query()->create([
                'name' => $categoryName,
                'description' => $categoryData['description'],
            ]);

            foreach ($categoryData['products'] as [$sku, $name, $price, $reorder, $inbound, $outbound]) {
                $product = Product::query()->create([
                    'category_id' => $category->id,
                    'sku' => $sku,
                    'name' => $name,
                    'description' => $name.' from the Aruna pantry.',
                    'unit_price' => $price,
                    'reorder_level' => $reorder,
                    'is_active' => true,
                ]);

                $this->receive($product, $manager?->id, (int) round($inbound * 0.7), now()->subDays(13)->setTime(9, 0));
                $this->receive($product, $manager?->id, $inbound - (int) round($inbound * 0.7), now()->subDays(3)->setTime(11, 30));
                $this->ship($product, $manager?->id, $outbound);
            }
        }

        $seasonal = Category::query()->create([
            'name' => 'Seasonal',
            'description' => 'Limited runs that are no longer on the active count.',
        ]);

        $sample = Product::query()->create([
            'category_id' => $seasonal->id,
            'sku' => 'ARU-ADV-01',
            'name' => 'Advent Sample',
            'description' => 'Archived holiday sample. Kept for the ledger, hidden from the active count.',
            'unit_price' => 6.00,
            'reorder_level' => 4,
            'is_active' => false,
        ]);

        $this->receive($sample, $manager?->id, 5, now()->subDays(20)->setTime(9, 0));
    }

    private function receive(Product $product, ?int $userId, int $quantity, DateTimeInterface $when): void
    {
        if ($quantity < 1) {
            return;
        }

        StockMovement::query()->create([
            'product_id' => $product->id,
            'user_id' => $userId,
            'type' => StockMovementType::Inbound,
            'quantity' => $quantity,
            'note' => 'Receiving',
            'occurred_at' => $when,
        ]);
    }

    private function ship(Product $product, ?int $userId, int $quantity): void
    {
        if ($quantity < 1) {
            return;
        }

        $remaining = $quantity;
        $day = 12;

        while ($remaining > 0) {
            $chunk = min($remaining, max(1, (int) ceil($quantity / 6)));
            $remaining -= $chunk;

            StockMovement::query()->create([
                'product_id' => $product->id,
                'user_id' => $userId,
                'type' => StockMovementType::Outbound,
                'quantity' => $chunk,
                'note' => 'Counter sales',
                'occurred_at' => now()->subDays($day)->setTime(16, 15),
            ]);

            $day = max(1, $day - 2);
        }
    }
}
