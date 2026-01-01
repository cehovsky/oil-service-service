<?php

declare(strict_types=1);

namespace App\Modules\OilService\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\OilService\DBAL\Repository\CarRepository;

enum CarGridSortEnum: string implements OrderEnumInterface
{
    case LABEL = 'label';

    case IDENT = 'ident';

    case LICENSE_PLATE = 'licensePlate';

    case STATUS = 'status';

    case CREATED_AT = 'createdAt';

    public function toSql(): string
    {
        return CarRepository::ALIAS . '.' . $this->value;
    }
}
