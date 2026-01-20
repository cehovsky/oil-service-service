<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Enum;

enum ChatUserRequestStatusEnum: string
{
    public const array VALUES = [
        'open',
        'resolved',
    ];

    case OPEN = 'open';

    case RESOLVED = 'resolved';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
