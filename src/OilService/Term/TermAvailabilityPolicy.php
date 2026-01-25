<?php

declare(strict_types=1);

namespace App\OilService\Term;

use DateTimeImmutable;

final class TermAvailabilityPolicy
{
    public const int MIN_DAYS_AHEAD = 2;

    public function getMinimumAvailableDate(): DateTimeImmutable
    {
        $today = new DateTimeImmutable('today');

        return $today->modify(sprintf('+%d days', self::MIN_DAYS_AHEAD));
    }
}
