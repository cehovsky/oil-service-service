<?php

declare(strict_types=1);

namespace App\Modules\OilService\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\OilService\DBAL\Repository\UserRepository;

enum OilServiceUserGridSortEnum: string implements OrderEnumInterface
{
    case EMAIL = 'email';

    case FULL_NAME = 'fullName';

    case PHONE = 'phone';

    case CREATED_AT = 'createdAt';

    public function toSql(): string
    {
        return UserRepository::ALIAS . '.' . $this->value;
    }
}
