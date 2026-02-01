<?php

declare(strict_types=1);

namespace App\Core\Helper;

use App\Domain\Exception\InvalidDataException;
use ValueError;

class QueryParameterParser
{
    /**
     * @template T of \BackedEnum
     * @param class-string<T> $enumClass
     * @return T
     */
    public static function parseEnum(string $value, string $enumClass): mixed
    {
        try {
            return $enumClass::from($value);
        } catch (ValueError) {
            throw new InvalidDataException('Invalid enum value.');
        }
    }

    public static function parseBoolean(string $value): bool
    {
        if ($value === 'true' || $value === '1') {
            return true;
        }

        if ($value === 'false' || $value === '0') {
            return false;
        }

        throw new InvalidDataException('Invalid boolean value. Expected: true, false, 1, or 0.');
    }

    public static function parseNumeric(string $value): float
    {
        if (!is_numeric($value)) {
            throw new InvalidDataException('Invalid numeric value.');
        }

        return (float) $value;
    }

    public static function parseInteger(string $value): int
    {
        if (!is_numeric($value)) {
            throw new InvalidDataException('Invalid integer value.');
        }

        return (int) $value;
    }
}
