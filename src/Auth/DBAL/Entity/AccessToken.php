<?php

declare(strict_types=1);

namespace App\Auth\DBAL\Entity;

use App\Auth\DBAL\Repository\AccessTokenRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'auth_token_access')]
#[ORM\Entity(repositoryClass: AccessTokenRepository::class)]
#[ORM\Index(fields: ['expiresAt'], name: 'expires_at_index')]
class AccessToken
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(length: 36, unique: true, options: ['fixed' => true])]
    private string $token;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $expiresAt;

    #[ORM\ManyToOne(targetEntity: RefreshToken::class, inversedBy: 'accessTokens')]
    #[ORM\JoinColumn]
    private RefreshToken $refreshToken;

    public function __construct(
        Uuid $id,
        string $token,
        DateTimeImmutable $expiresAt,
        RefreshToken $refreshToken,
    ) {
        $this->id = $id;
        $this->token = $token;
        $this->expiresAt = $expiresAt;
        $this->refreshToken = $refreshToken;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getRefreshToken(): RefreshToken
    {
        return $this->refreshToken;
    }
}
