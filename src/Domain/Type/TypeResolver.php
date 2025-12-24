<?php

declare(strict_types=1);

namespace App\Domain\Type;

use Symfony\Component\Uid\Uuid;
use Throwable;

class TypeResolver
{
    public static function isUuid(string $value): bool
    {
        try {
            new Uuid($value);
        } catch (Throwable) {
            return false;
        }

        return true;
    }
}
