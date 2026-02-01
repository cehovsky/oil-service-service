<?php

declare(strict_types=1);

namespace App\Core\Helper;

class IdentParser
{
    /**
     * Normalize ident filter from various formats to integer.
     * Supports numeric format or formatted string like SYYXXXXX, RYYXXXXX, OYYXXXXX.
     */
    public static function normalizeIdentFilter(?string $ident): ?int
    {
        if ($ident === null) {
            return null;
        }

        $trimmed = trim($ident);

        if ($trimmed === '') {
            return null;
        }

        if (is_numeric($trimmed)) {
            return (int) $trimmed;
        }

        if (preg_match('/^[A-Za-z]+(\d{2})(\d+)$/', $trimmed, $matches) === 1) {
            return (int) $matches[2];
        }

        return null;
    }
}
