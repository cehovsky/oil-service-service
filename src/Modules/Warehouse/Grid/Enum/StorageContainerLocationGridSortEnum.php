<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\Warehouse\DBAL\Repository\StorageContainerLocationRepository;

enum StorageContainerLocationGridSortEnum: string implements OrderEnumInterface
{
    case MOVED_AT = 'movedAt';

    case CREATED_AT = 'createdAt';

    case UPDATED_AT = 'updatedAt';

    case STORAGE_CONTAINER = 'storageContainer';

    case WAREHOUSE = 'warehouse';

    case ROUTE = 'route';

    public function toSql(): string
    {
        return match ($this) {
            self::STORAGE_CONTAINER => StorageContainerLocationRepository::ALIAS . '.storageContainer',
            self::WAREHOUSE => StorageContainerLocationRepository::ALIAS . '.warehouse',
            self::ROUTE => StorageContainerLocationRepository::ALIAS . '.route',
            default => StorageContainerLocationRepository::ALIAS . '.' . $this->value,
        };
    }
}
