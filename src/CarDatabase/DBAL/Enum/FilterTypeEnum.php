<?php

declare(strict_types=1);

namespace App\CarDatabase\DBAL\Enum;

enum FilterTypeEnum: string
{
    public const array VALUES = [
        'oil',
        'air',
        'fuel',
        'cabin',
    ];

    case OIL = 'oil';
    case AIR = 'air';
    case FUEL = 'fuel';
    case CABIN = 'cabin';

    public static function values(): array
    {
        return self::VALUES;
    }
}
