<?php

declare(strict_types=1);

namespace App\Domain;

final class CrcGenerator
{
    public function generate(string $payload): string
    {
        return hash('crc32b', $payload);
    }
}
