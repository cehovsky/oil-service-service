<?php

declare(strict_types=1);

namespace App\Modules\Auth\DTO;

class RefreshTokenResponseDTO
{
    public function __construct(
        private readonly string $accessToken,
        private readonly int $ttlSeconds,
    ) {
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getTtlSeconds(): int
    {
        return $this->ttlSeconds;
    }
}
