<?php

declare(strict_types=1);

namespace App\Modules\Users\Grid\Enum;

use App\Auth\DBAL\Repository\UserRepository;
use App\Domain\ApiGrid\OrderEnumInterface;

enum UsersGridSortEnum: string implements OrderEnumInterface
{
    case EMAIL = 'email';

    case FULL_NAME = 'fullName';

    case IS_ACTIVE = 'isActive';

    case IS_ADMIN = 'isAdmin';

    public function toSql(): string
    {
        return UserRepository::ALIAS . '.' . $this->value;
    }
}
