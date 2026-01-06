<?php

declare(strict_types=1);

namespace App\Modules\OilService\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\OilService\DBAL\Repository\OrderRepository;

enum OrderGridSortEnum: string implements OrderEnumInterface
{
    case IDENT = 'ident';

    case FULL_NAME = 'fullName';

    case EMAIL = 'email';

    case PHONE = 'phone';

    case CAR_MODEL = 'carModel';

    case LICENSE_PLATE = 'licensePlate';

    case IS_COMPANY = 'isCompany';

    case REALIZATION_DATE = 'realizationDate';

    case CREATED_AT = 'createdAt';

    public function toSql(): string
    {
        return OrderRepository::ALIAS . '.' . $this->value;
    }
}
