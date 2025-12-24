<?php

declare(strict_types=1);

namespace App\Modules\Auth\DTO;

class AuthenticateResponseDTO
{
    public function __construct(
        private readonly string $refreshToken,
        private readonly string $accessToken,
        private readonly int $refreshTokenTtlSeconds,
        private readonly int $accessTokenTtlSeconds,
    ) {
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshTokenTtlSeconds(): int
    {
        return $this->refreshTokenTtlSeconds;
    }

    public function getAccessTokenTtlSeconds(): int
    {
        return $this->accessTokenTtlSeconds;
    }
}
