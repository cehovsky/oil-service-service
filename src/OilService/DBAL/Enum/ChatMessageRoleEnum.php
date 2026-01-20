<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Enum;

enum ChatMessageRoleEnum: string
{
    public const array VALUES = [
        'system',
        'user',
        'assistant',
        'note',
    ];

    case SYSTEM = 'system';

    case USER = 'user';

    case ASSISTANT = 'assistant';

    case NOTE = 'note';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
