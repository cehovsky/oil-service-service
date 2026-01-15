<?php

declare(strict_types=1);

namespace App\Modules\OilService\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\OilService\DBAL\Repository\PriceListItemRepository;

enum PriceListItemGridSortEnum: string implements OrderEnumInterface
{
    case LABEL = 'label';
    case PRICE = 'price';
    case VAT = 'vat';
    case PRICE_VAT = 'priceVat';
    case IS_ACTIVE = 'isActive';
    case IS_PUBLIC = 'isPublic';
    case IS_DEFAULT = 'isDefault';
    case IS_HIDDEN_ON_INVOICE = 'isHiddenOnInvoice';
    case CODE = 'code';
    case BRAND = 'brand';
    case EXTERNAL_CODE = 'externalCode';
    case CREATED_AT = 'createdAt';

    public function toSql(): string
    {
        return PriceListItemRepository::ALIAS . '.' . $this->value;
    }
}
