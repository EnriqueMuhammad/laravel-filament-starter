<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleName: string
{
    case SuperAdmin = 'super_admin';
    case Manager = 'manager';
    case Viewer = 'viewer';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super admin',
            self::Manager => 'Inventory manager',
            self::Viewer => 'Auditor',
        };
    }
}
