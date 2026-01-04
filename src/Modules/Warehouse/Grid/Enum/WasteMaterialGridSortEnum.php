<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\Warehouse\DBAL\Repository\WasteMaterialRepository;

enum WasteMaterialGridSortEnum: string implements OrderEnumInterface
{
    case CODE = 'code';

    case LABEL = 'label';

    case SHORT_LABEL = 'shortLabel';

    case VOLUME_UNIT = 'volumeUnit';

    case IS_ACTIVE = 'isActive';

    case CREATED_AT = 'createdAt';

    case UPDATED_AT = 'updatedAt';

    public function toSql(): string
    {
        return WasteMaterialRepository::ALIAS . '.' . $this->value;
    }
}
