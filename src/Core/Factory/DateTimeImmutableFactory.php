<?php

declare(strict_types=1);

namespace App\Core\Factory;

use App\Domain\Exception\InvalidDateTimeException;
use DateTimeImmutable;
use Exception;

class DateTimeImmutableFactory
{
    public const string ISO_8601_FORMAT = 'Y-m-d\TH:i:s\Z';

    /**
     * @throws InvalidDateTimeException
     */
    public function create(string $dateTimeString): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($dateTimeString);
        } catch (Exception) {
            throw InvalidDateTimeException::createFromString($dateTimeString);
        }
    }

    public function createNow(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    public function createFromTimestamp(int $timestamp): DateTimeImmutable
    {
        return (new DateTimeImmutable())->setTimestamp($timestamp);
    }
}
