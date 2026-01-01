<?php

declare(strict_types=1);

namespace App\Modules\OilService\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\OilService\DBAL\Repository\RouteRepository;

enum RouteGridSortEnum: string implements OrderEnumInterface
{
    case DATE = 'date';

    case IS_ACTIVE = 'isActive';

    case CREATED_AT = 'createdAt';

    public function toSql(): string
    {
        return RouteRepository::ALIAS . '.' . $this->value;
    }
}
