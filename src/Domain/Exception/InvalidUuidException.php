<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Exception;
use Stringable;

final class InvalidUuidException extends Exception
{
    public function __construct(mixed $invalidUuid)
    {
        $invalidUuid = is_string($invalidUuid) || $invalidUuid instanceof Stringable
            ? (string) $invalidUuid
            : '[Unstringable]';

        parent::__construct("Uuid '$invalidUuid' is invalid!");
    }
}
