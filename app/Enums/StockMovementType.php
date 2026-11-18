<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StockMovementType: string implements HasColor, HasLabel
{
    case Inbound = 'inbound';
    case Outbound = 'outbound';
    case Adjustment = 'adjustment';

    public function getLabel(): string
    {
        return match ($this) {
            self::Inbound => 'Inbound',
            self::Outbound => 'Outbound',
            self::Adjustment => 'Adjustment',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Inbound => 'success',
            self::Outbound => 'danger',
            self::Adjustment => 'warning',
        };
    }
}
