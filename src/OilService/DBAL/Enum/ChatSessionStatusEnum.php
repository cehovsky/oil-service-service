<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Enum;

enum ChatSessionStatusEnum: string
{
    public const array VALUES = [
        'active',
        'completed',
        'expired',
    ];

    case ACTIVE = 'active';

    case COMPLETED = 'completed';

    case EXPIRED = 'expired';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
