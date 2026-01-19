<?php

declare(strict_types=1);

namespace App\Modules\OilService\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\OilService\DBAL\Repository\InventoryItemRepository;

enum InventoryItemGridSortEnum: string implements OrderEnumInterface
{
    case LABEL = 'label';
    case PRICE = 'price';
    case VAT = 'vat';
    case PRICE_VAT = 'priceVat';
    case STOCK_COUNT = 'stockCount';
    case CREATED_AT = 'createdAt';
    case UPDATED_AT = 'updatedAt';

    public function toSql(): string
    {
        return InventoryItemRepository::ALIAS . '.' . $this->value;
    }
}
