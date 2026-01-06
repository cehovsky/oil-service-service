<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Enum;

enum RealizationTimeSlotEnum: string
{
    public const array VALUES = [
        'morning',
        'lunch',
        'afternoon',
    ];

    case MORNING = 'morning';

    case LUNCH = 'lunch';

    case AFTERNOON = 'afternoon';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
