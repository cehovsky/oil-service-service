<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Enum;

enum CarStatusEnum: string
{
    public const array VALUES = [
        'preparing',
        'operational',
        'out_of_order',
        'removed',
    ];

    case PREPARING = 'preparing';

    case OPERATIONAL = 'operational';

    case OUT_OF_ORDER = 'out_of_order';

    case REMOVED = 'removed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
