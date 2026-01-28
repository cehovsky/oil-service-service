<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Enum;

enum OrderStatusEnum: string
{
    public const array VALUES = [
        'new',
        'in_preparation',
        'in_process',
        'completed',
        'canceled',
    ];

    case NEW = 'new';

    case IN_PREPARATION = 'in_preparation';

    case IN_PROCESS = 'in_process';

    case COMPLETED = 'completed';

    case CANCELED = 'canceled';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
