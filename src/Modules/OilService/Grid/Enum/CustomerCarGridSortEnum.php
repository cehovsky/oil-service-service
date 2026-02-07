<?php

declare(strict_types=1);

namespace App\Modules\OilService\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\OilService\DBAL\Repository\CustomerCarRepository;

enum CustomerCarGridSortEnum: string implements OrderEnumInterface
{
    case LICENSE_PLATE = 'licensePlate';

    case BRAND = 'brand';

    case MODEL = 'model';

    case VIN = 'vin';

    case CREATED_AT = 'createdAt';

    public function toSql(): string
    {
        return CustomerCarRepository::ALIAS . '.' . $this->value;
    }
}
