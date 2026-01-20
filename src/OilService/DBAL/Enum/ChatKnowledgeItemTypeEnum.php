<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Enum;

enum ChatKnowledgeItemTypeEnum: string
{
    public const array VALUES = [
        'knowledge',
        'greeting',
        'system',
    ];

    case KNOWLEDGE = 'knowledge';

    case GREETING = 'greeting';

    case SYSTEM = 'system';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
