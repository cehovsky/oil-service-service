<?php

declare(strict_types=1);

namespace App\Modules\Auth\Factory;

use App\Auth\DBAL\Entity\AccessToken;
use App\Auth\DBAL\Entity\User;
use App\Modules\Auth\DTO\AuthenticateResponseDTO;
use App\Modules\Auth\DTO\TokenInfoResponseDTO;
use App\Modules\Auth\DTO\RefreshTokenResponseDTO;

class DTOFactory
{
    public function createAuthAuthenticateResponseDTO(AccessToken $accessToken): AuthenticateResponseDTO
    {
        $refreshToken = $accessToken->getRefreshToken();
        $now = time();

        return new AuthenticateResponseDTO(
            $refreshToken->getToken(),
            $accessToken->getToken(),
            $refreshToken->getExpiresAt()->getTimestamp() - $now,
            $accessToken->getExpiresAt()->getTimestamp() - $now
        );
    }

    public function createAuthTokenInfoResponseDTO(User $user): TokenInfoResponseDTO
    {
        return new TokenInfoResponseDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getFullName(),
            $user->getIsAdmin(),
            $user->getIsOffice()
        );
    }

    public function createAuthRefreshTokenResponseDTO(AccessToken $accessToken): RefreshTokenResponseDTO
    {
        $now = time();

        return new RefreshTokenResponseDTO(
            $accessToken->getToken(),
            $accessToken->getExpiresAt()->getTimestamp() - $now
        );
    }
}
