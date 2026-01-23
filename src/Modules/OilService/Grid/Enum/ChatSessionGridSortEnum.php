<?php

declare(strict_types=1);

namespace App\Modules\OilService\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\OilService\DBAL\Repository\ChatSessionRepository;

enum ChatSessionGridSortEnum: string implements OrderEnumInterface
{
    case IDENT = 'ident';
    case ID = 'id';
    case STATUS = 'status';
    case LANGUAGE = 'language';
    case CREATED_AT = 'createdAt';
    case UPDATED_AT = 'updatedAt';
    case CLOSED_AT = 'closedAt';

    public function toSql(): string
    {
        return ChatSessionRepository::ALIAS . '.' . $this->value;
    }
}
