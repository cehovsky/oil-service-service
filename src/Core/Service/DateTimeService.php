<?php

declare(strict_types=1);

namespace App\Core\Service;

use App\Domain\Exception\InvalidDataException;
use DateTimeImmutable;

class DateTimeService
{
    public function createDateFromString(string $dateString): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $dateString);

        if ($date === false) {
            throw new InvalidDataException('Invalid date format. Expected: Y-m-d');
        }

        return $date;
    }

    public function validateYearMonth(int $year, int $month): void
    {
        if ($year <= 0 || $month < 1 || $month > 12) {
            throw new InvalidDataException('Invalid month or year.');
        }
    }

    public function createMonthRange(int $year, int $month): array
    {
        $this->validateYearMonth($year, $month);

        $start = (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))->setTime(0, 0);
        $end = $start->modify('last day of this month');

        return ['start' => $start, 'end' => $end];
    }
}
