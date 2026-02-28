<?php

declare(strict_types=1);

namespace App\Sepno\DBAL\Enum;

enum SepnoRecordStatusEnum: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case CLOSED = 'closed';
}
