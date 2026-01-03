<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Enum;

enum StorageVolumeUnitEnum: string
{
    public const array VALUES = [
        'l',
        'kg',
        'ks',
    ];

    case L = 'l';

    case KG = 'kg';

    case KS = 'ks';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
