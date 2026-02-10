<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\Grid\Enum;

use App\CarDatabase\DBAL\Repository\FilterRepository;
use App\Domain\ApiGrid\OrderEnumInterface;

enum FilterGridSortEnum: string implements OrderEnumInterface
{
    case FILTER_TYPE = 'filterType';
    case MANUFACTURER = 'manufacturer';
    case CODE = 'code';
    case OEM_CODE = 'oemCode';
    case CREATED_AT = 'createdAt';
    case UPDATED_AT = 'updatedAt';

    public function toSql(): string
    {
        return FilterRepository::ALIAS . '.' . $this->value;
    }
}
