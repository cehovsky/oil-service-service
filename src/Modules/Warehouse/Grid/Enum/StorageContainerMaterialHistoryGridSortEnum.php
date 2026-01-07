<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\Warehouse\DBAL\Repository\StorageContainerMaterialHistoryRepository;

enum StorageContainerMaterialHistoryGridSortEnum: string implements OrderEnumInterface
{
    case CREATED_AT = 'createdAt';

    case STORAGE_CONTAINER = 'storageContainer';

    case CREATED_BY = 'createdBy';

    public function toSql(): string
    {
        return match ($this) {
            self::STORAGE_CONTAINER => StorageContainerMaterialHistoryRepository::ALIAS . '.storageContainer',
            self::CREATED_BY => StorageContainerMaterialHistoryRepository::ALIAS . '.createdBy',
            default => StorageContainerMaterialHistoryRepository::ALIAS . '.' . $this->value,
        };
    }
}
