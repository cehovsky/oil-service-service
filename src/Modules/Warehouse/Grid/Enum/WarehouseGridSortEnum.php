<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\Warehouse\DBAL\Repository\WarehouseRepository;

enum WarehouseGridSortEnum: string implements OrderEnumInterface
{
    case LABEL = 'label';

    case SHORT_LABEL = 'shortLabel';

    case IS_ACTIVE = 'isActive';

    case CREATED_AT = 'createdAt';

    case UPDATED_AT = 'updatedAt';

    public function toSql(): string
    {
        return WarehouseRepository::ALIAS . '.' . $this->value;
    }
}
