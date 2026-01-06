<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Enum;

enum OrderStatusEnum: string
{
    public const array VALUES = [
        'new',
        'in_preparation',
        'to_process',
        'to_complete',
        'completed',
        'canceled',
    ];

    case NEW = 'new';

    case IN_PREPARATION = 'in_preparation';

    case TO_PROCESS = 'to_process';

    case TO_COMPLETE = 'to_complete';

    case COMPLETED = 'completed';

    case CANCELED = 'canceled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
