<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Enum;

enum InventoryMovementTypeEnum: string
{
    public const array VALUES = [
        'stock_in',
        'order_transfer',
        'stock_out',
    ];

    case STOCK_IN = 'stock_in';

    case ORDER_TRANSFER = 'order_transfer';

    case STOCK_OUT = 'stock_out';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
