<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\Warehouse\DBAL\Repository\StorageContainerRepository;

enum StorageContainerGridSortEnum: string implements OrderEnumInterface
{
    case CODE = 'code';

    case DESCRIPTION = 'description';

    case IS_ACTIVE = 'isActive';

    case TYPE = 'type';

    case CAPACITY = 'capacity';

    case VOLUME_UNIT = 'volumeUnit';

    case CREATED_AT = 'createdAt';

    case UPDATED_AT = 'updatedAt';

    public function toSql(): string
    {
        return StorageContainerRepository::ALIAS . '.' . $this->value;
    }
}
