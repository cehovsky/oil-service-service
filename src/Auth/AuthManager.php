<?php

declare(strict_types=1);

namespace App\Auth;

use App\Auth\DBAL\Entity\AccessToken;
use App\Auth\DBAL\Entity\RefreshToken;
use App\Auth\DBAL\Entity\User;
use App\Auth\DBAL\Repository\AccessTokenRepository;
use App\Auth\DBAL\Repository\RefreshTokenRepository;
use App\Auth\DBAL\Repository\UserRepository;
use App\Auth\Exception\AuthenticationFailedException;
use App\Auth\Factory\EntityFactory;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Exception\UnauthorizedException;
use App\Domain\Exception\UnexpectedException;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Throwable;

class AuthManager
{
    public function __construct(
        private readonly RefreshTokenRepository $refreshTokenRepository,
        private readonly AccessTokenRepository $accessTokenRepository,
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws AuthenticationFailedException
     */
    public function authenticateUserLocally(string $email, string $password): User
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            throw new AuthenticationFailedException('User not found.');
        }

        if (!password_verify($password, $user->getPassword())) {
            throw new AuthenticationFailedException('Invalid credentials.');
        }

        $this->tryCatchUnauthorizedUser($user);

        return $user;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createAccessTokenWithRefreshTokenAndFlush(User $user): AccessToken
    {
        $refreshToken = $this->entityFactory->createRefreshToken($user);
        $accessToken = $this->entityFactory->createAccessToken($refreshToken);

        $this->entityManager->persist($refreshToken);
        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();

        return $accessToken;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function createAccessTokenAndFlush(RefreshToken $refreshToken): AccessToken
    {
        $accessToken = $this->entityFactory->createAccessToken($refreshToken);

        $this->entityManager->persist($accessToken);
        $this->entityManager->flush();

        return $accessToken;
    }

    /**
     * @throws UnauthorizedException
     * @throws UnexpectedException
     */
    public function authenticateRefreshTokenFromString(string $refreshTokenString): RefreshToken
    {
        $refreshToken = $this->refreshTokenRepository->findByToken($refreshTokenString);

        if ($refreshToken === null) {
            throw new UnauthorizedException(
                sprintf(
                    "Cannot find refresh token '%s'",
                    $refreshTokenString
                )
            );
        }

        $this->tryCatchUnauthorizedRefreshToken($refreshToken);
        $this->tryCatchUnauthorizedUser($refreshToken->getUser());

        return $refreshToken;
    }

    /**
     * @throws UnauthorizedException
     * @throws UnexpectedException
     */
    public function authenticateAccessTokenFromString(string $accessToken): AccessToken
    {
        $accessTokenEntity = $this->accessTokenRepository->findByToken($accessToken);

        if ($accessTokenEntity === null) {
            throw new UnauthorizedException(
                sprintf("Cannot find access token '%s'", $accessToken)
            );
        }

        $this->tryCatchUnauthorizedAccessToken($accessTokenEntity);
        $this->tryCatchUnauthorizedUser($accessTokenEntity->getRefreshToken()->getUser());

        return $accessTokenEntity;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function rejectRefreshTokenAndFlush(RefreshToken $refreshToken): void
    {
        $refreshToken->setIsRejected(true);
        $this->entityManager->flush();
    }

    /**
     * @throws UnauthorizedException
     * @throws UnexpectedException
     */
    private function tryCatchUnauthorizedRefreshToken(RefreshToken $refreshToken): void
    {
        try {
            $dateTimeNow = new DateTimeImmutable();

            if ($refreshToken->getIsRejected()) {
                throw new UnauthorizedException(
                    sprintf(
                        "Refresh token '%s' was rejected.",
                        $refreshToken->getToken()
                    )
                );
            }

            if ($dateTimeNow > $refreshToken->getExpiresAt()) {
                throw new UnauthorizedException(
                    sprintf(
                        "Refresh token '%s' is expired.",
                        $refreshToken->getToken()
                    )
                );
            }
        } catch (UnauthorizedException $e) {
            throw new UnauthorizedException($e);
        } catch (Throwable $e) {
            throw new UnexpectedException($e);
        }
    }

    /**
     * @throws UnauthorizedException
     * @throws UnexpectedException
     */
    private function tryCatchUnauthorizedAccessToken(AccessToken $accessToken): void
    {
        try {
            $dateTimeNow = new DateTimeImmutable();

            if ($accessToken->getRefreshToken()->getIsRejected()) {
                throw new UnauthorizedException(
                    sprintf(
                        "Access token '%s' was rejected by refresh token.",
                        $accessToken->getToken()
                    )
                );
            }

            if ($dateTimeNow > $accessToken->getExpiresAt()) {
                throw new UnauthorizedException(
                    sprintf(
                        "Access token '%s' has expired.",
                        $accessToken->getToken()
                    )
                );
            }
        } catch (UnauthorizedException $e) {
            throw new UnauthorizedException($e);
        } catch (Throwable $e) {
            throw new UnexpectedException($e);
        }
    }

    /**
     * @throws UnauthorizedException
     */
    public function tryCatchUnauthorizedUser(User $user): void
    {
        if (!$user->getIsActive()) {
            throw new UnauthorizedException(
                sprintf(
                    'User with id %s is not active.',
                    $user->getId()
                )
            );
        }
    }
}
