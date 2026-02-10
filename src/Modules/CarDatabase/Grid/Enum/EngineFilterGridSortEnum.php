<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\Grid\Enum;

use App\CarDatabase\DBAL\Repository\EngineFilterRepository;
use App\Domain\ApiGrid\OrderEnumInterface;

enum EngineFilterGridSortEnum: string implements OrderEnumInterface
{
    case IS_PRIMARY = 'isPrimary';
    case CREATED_AT = 'createdAt';
    case UPDATED_AT = 'updatedAt';

    public function toSql(): string
    {
        return EngineFilterRepository::ALIAS . '.' . $this->value;
    }
}
