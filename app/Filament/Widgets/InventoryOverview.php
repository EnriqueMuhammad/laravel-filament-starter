<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Product;
use App\Models\StockMovement;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InventoryOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $active = Product::query()->where('is_active', true);
        $low = (clone $active)->lowStock()->count();
        $units = (int) (clone $active)->sum('quantity_on_hand');
        $value = (clone $active)->get(['unit_price', 'quantity_on_hand'])
            ->sum(fn (Product $product): float => (float) $product->unit_price * max($product->quantity_on_hand, 0));

        $trend = collect(range(6, 0))
            ->map(fn (int $daysAgo): int => StockMovement::query()
                ->whereDate('occurred_at', today()->subDays($daysAgo))
                ->count())
            ->all();

        return [
            Stat::make('Active products', number_format((clone $active)->count()))
                ->description($low === 0 ? 'Nothing is under its reorder level' : $low.' at or under reorder')
                ->descriptionIcon($low === 0 ? Heroicon::OutlinedCheckCircle : Heroicon::OutlinedExclamationTriangle)
                ->color($low === 0 ? 'success' : 'warning'),
            Stat::make('Units on hand', number_format($units))
                ->description('Active catalog only')
                ->descriptionIcon(Heroicon::OutlinedCube)
                ->color('primary'),
            Stat::make('Inventory value', '$'.number_format($value, 2))
                ->description('On-hand units at unit price')
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->color('primary'),
            Stat::make('Movements today', (string) StockMovement::query()->whereDate('occurred_at', today())->count())
                ->description('Last 7 days')
                ->chart($trend)
                ->color('info'),
        ];
    }
}
