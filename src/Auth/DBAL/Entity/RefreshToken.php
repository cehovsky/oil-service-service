<?php

declare(strict_types=1);

namespace App\Auth\DBAL\Entity;

use App\Auth\DBAL\Repository\RefreshTokenRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'auth_token_refresh')]
#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
#[ORM\Index(fields: ['expiresAt'], name: 'expires_at_index')]
#[ORM\Index(fields: ['isRejected'], name: 'is_rejected_index')]
class RefreshToken
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(length: 36, unique: true, options: ['fixed' => true])]
    private string $token;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $expiresAt;

    #[ORM\Column]
    private bool $isRejected;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'refreshTokens')]
    #[ORM\JoinColumn]
    private User $user;

    /** @var Collection<int, AccessToken> */
    #[ORM\OneToMany(mappedBy: 'refreshToken', targetEntity: AccessToken::class)]
    private Collection $accessTokens;

    public function __construct(
        Uuid $id,
        string $token,
        DateTimeImmutable $expiresAt,
        User $user,
        bool $isRejected = false
    ) {
        $this->id = $id;
        $this->token = $token;
        $this->expiresAt = $expiresAt;
        $this->user = $user;
        $this->isRejected = $isRejected;

        $this->accessTokens = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(
        string $token
    ): self {
        $this->token = $token;

        return $this;
    }

    public function getExpiresAt(): DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(
        User $user
    ): self {
        $this->user = $user;

        return $this;
    }

    public function getIsRejected(): bool
    {
        return $this->isRejected;
    }

    public function setIsRejected(
        bool $isRejected
    ): self {
        $this->isRejected = $isRejected;

        return $this;
    }

    /**
     * @return Collection<int, AccessToken>
     */
    public function getAccessTokens(): Collection
    {
        return $this->accessTokens;
    }
}
