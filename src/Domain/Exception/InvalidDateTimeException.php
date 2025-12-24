<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Exception;

final class InvalidDateTimeException extends Exception
{
    public static function createFromString(string $dateTimeString): self
    {
        return new self("Date time could not be created from string: $dateTimeString");
    }
}
