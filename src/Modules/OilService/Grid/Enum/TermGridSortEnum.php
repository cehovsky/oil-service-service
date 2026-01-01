<?php

declare(strict_types=1);

namespace App\Modules\OilService\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\OilService\DBAL\Repository\TermRepository;

enum TermGridSortEnum: string implements OrderEnumInterface
{
    case DATE = 'date';

    case TIME_SLOT = 'timeSlot';

    case IS_ACTIVE = 'isActive';

    case MAX_COUNT = 'maxCount';

    case CREATED_AT = 'createdAt';

    public function toSql(): string
    {
        return TermRepository::ALIAS . '.' . $this->value;
    }
}
