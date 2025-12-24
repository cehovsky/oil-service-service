<?php

declare(strict_types=1);

namespace App\Domain\Formatting;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class DateTimeFormatter
{
    public function toStringUtcNullable(?DateTimeImmutable $date): ?string
    {
        return $date?->setTimezone(new DateTimeZone('UTC'))->format(DateTimeInterface::RFC3339);
    }
}
