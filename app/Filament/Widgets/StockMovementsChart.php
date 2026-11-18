<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\StockMovementType;
use App\Models\StockMovement;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class StockMovementsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Stock movement';

    protected ?string $description = 'Units received and shipped over the last 14 days.';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '320px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $start = today()->subDays(13);

        $movements = StockMovement::query()
            ->where('occurred_at', '>=', $start->copy()->startOfDay())
            ->whereIn('type', [StockMovementType::Inbound, StockMovementType::Outbound])
            ->get(['type', 'quantity', 'occurred_at']);

        $labels = [];
        $inbound = [];
        $outbound = [];

        for ($day = 0; $day < 14; $day++) {
            $date = $start->copy()->addDays($day);
            $labels[] = $date->format('M j');

            $onDay = $movements->filter(
                fn (StockMovement $movement): bool => $movement->occurred_at instanceof Carbon
                    && $movement->occurred_at->isSameDay($date)
            );

            $inbound[] = (int) $onDay
                ->where('type', StockMovementType::Inbound)
                ->sum(fn (StockMovement $movement): int => abs($movement->quantity));

            $outbound[] = (int) $onDay
                ->where('type', StockMovementType::Outbound)
                ->sum(fn (StockMovement $movement): int => abs($movement->quantity));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Inbound',
                    'data' => $inbound,
                    'backgroundColor' => '#0f6e6a',
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Outbound',
                    'data' => $outbound,
                    'backgroundColor' => '#c46b3a',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
