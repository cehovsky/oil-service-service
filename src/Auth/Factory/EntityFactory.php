<?php

declare(strict_types=1);

namespace App\Auth\Factory;

use App\Auth\DBAL\Entity\AccessToken;
use App\Auth\DBAL\Entity\RefreshToken;
use App\Auth\DBAL\Entity\User;
use App\Domain\Exception\InvalidArgumentException;
use DateTimeImmutable;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Uid\Factory\UuidFactory;

class EntityFactory
{
    public function __construct(
        private readonly UuidFactory $uuidFactory,
        #[Autowire(env: 'AUTH_TOKEN_REFRESH_EXPIRES')]
        private readonly string $refreshTokenExpiration,
        #[Autowire(env: 'AUTH_TOKEN_ACCESS_EXPIRES')]
        private readonly string $accessTokenExpiration,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createRefreshToken(User $user): RefreshToken
    {
        return new RefreshToken(
            $this->uuidFactory->timeBased()->create(),
            $this->uuidFactory->create()->toRfc4122(),
            $this->getRefreshTokenExpiresAt(),
            $user,
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createAccessToken(RefreshToken $refreshToken): AccessToken
    {
        return new AccessToken(
            $this->uuidFactory->timeBased()->create(),
            $this->uuidFactory->create()->toRfc4122(),
            $this->getAccessTokenExpiresAt(),
            $refreshToken,
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getRefreshTokenExpiresAt(): DateTimeImmutable
    {
        $refreshTokenExpirationValue = sprintf('+ %s seconds', $this->refreshTokenExpiration);

        try {
            return new DateTimeImmutable($refreshTokenExpirationValue);
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot convert refreshTokenExpiration to DateTime with value "%s"',
                    $refreshTokenExpirationValue,
                ),
                $e->getCode(),
                $e,
            );
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getAccessTokenExpiresAt(): DateTimeImmutable
    {
        $accessTokenExpirationValue = sprintf('+ %s seconds', $this->accessTokenExpiration);

        try {
            return new DateTimeImmutable($accessTokenExpirationValue);
        } catch (Exception $e) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot convert accessTokenExpiration to DateTime with value "%s"',
                    $accessTokenExpirationValue,
                ),
                $e->getCode(),
                $e,
            );
        }
    }

    public function createUser(
        string $email,
        string $hashedPassword,
        string $fullName,
        bool $isActive = true,
        bool $isAdmin = false,
    ): User {
        return new User(
            $this->uuidFactory->timeBased()->create(),
            $email,
            $hashedPassword,
            $fullName,
            $isActive,
            $isAdmin,
        );
    }
}
