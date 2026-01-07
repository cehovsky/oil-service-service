<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\Warehouse\DBAL\Repository\RecyclingRepository;

enum RecyclingGridSortEnum: string implements OrderEnumInterface
{
    case RECYCLED_AT = 'recycledAt';

    case CREATED_AT = 'createdAt';

    case UPDATED_AT = 'updatedAt';

    public function toSql(): string
    {
        return RecyclingRepository::ALIAS . '.' . $this->value;
    }
}
