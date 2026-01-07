<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\Warehouse\DBAL\Repository\StorageContainerMaterialRepository;

enum StorageContainerMaterialGridSortEnum: string implements OrderEnumInterface
{
    case CREATED_AT = 'createdAt';

    case UPDATED_AT = 'updatedAt';

    case VOLUME = 'volume';

    case IS_RECYCLED = 'isRecycled';

    case STORAGE_CONTAINER = 'storageContainer';

    case WASTE_MATERIAL = 'wasteMaterial';

    case WAREHOUSE = 'warehouse';

    case ROUTE = 'route';

    case RECYCLING = 'recycling';

    public function toSql(): string
    {
        return match ($this) {
            self::STORAGE_CONTAINER => StorageContainerMaterialRepository::ALIAS . '.storageContainer',
            self::WASTE_MATERIAL => StorageContainerMaterialRepository::ALIAS . '.wasteMaterial',
            self::WAREHOUSE => StorageContainerMaterialRepository::ALIAS . '.warehouse',
            self::ROUTE => StorageContainerMaterialRepository::ALIAS . '.route',
            self::RECYCLING => StorageContainerMaterialRepository::ALIAS . '.recycling',
            default => StorageContainerMaterialRepository::ALIAS . '.' . $this->value,
        };
    }
}
