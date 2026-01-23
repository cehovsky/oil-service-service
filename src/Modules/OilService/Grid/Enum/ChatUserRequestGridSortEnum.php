<?php

declare(strict_types=1);

namespace App\Modules\OilService\Grid\Enum;

use App\Domain\ApiGrid\OrderEnumInterface;
use App\OilService\DBAL\Repository\ChatUserRequestRepository;

enum ChatUserRequestGridSortEnum: string implements OrderEnumInterface
{
    case IDENT = 'ident';
    case ID = 'id';
    case STATUS = 'status';
    case CONTENT = 'content';
    case CREATED_AT = 'createdAt';
    case RESOLVED_AT = 'resolvedAt';
    case IS_RESOLVED = 'isResolved';
    case NOTE = 'note';
    case SESSION_ID = 'session';
    case SESSION_IDENT = 'sessionIdent';
    case SESSION_LANGUAGE = 'sessionLanguage';

    public function toSql(): string
    {
        return match ($this) {
            self::SESSION_ID => ChatUserRequestRepository::ALIAS . '.session',
            self::SESSION_IDENT => 'session.ident',
            self::SESSION_LANGUAGE => 'session.language',
            default => ChatUserRequestRepository::ALIAS . '.' . $this->value,
        };
    }
}
