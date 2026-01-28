<?php

declare(strict_types=1);

namespace App\Auth\DBAL\Entity;

use App\Auth\DBAL\Repository\UserRepository;
use App\Auth\Validation\Constraint as AuthAssert;
use App\Files\DBAL\Entity\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[AuthAssert\EmailDuplicity]
#[ORM\Table(name: 'auth_user')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\Email(message: 'Email is not a valid.')]
    #[ORM\Column(length: 180, unique: true)]
    private string $email;

    #[ORM\Column(length: 255)]
    private string $password;

    #[ORM\Column]
    private string $fullName;

    #[ORM\Column]
    private bool $isActive;

    #[ORM\Column(options: ['default' => false])]
    private bool $isAdmin = false;

    #[ORM\Column(options: ['default' => false])]
    private bool $isOffice = false;

    /** @var Collection<int, RefreshToken> */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: RefreshToken::class, cascade: ['persist'])]
    private Collection $refreshTokens;

    /** @var Collection<int, File> */
    #[ORM\OneToMany(mappedBy: 'createdUser', targetEntity: File::class)]
    private Collection $files;

    public function __construct(
        Uuid $id,
        string $email,
        string $password,
        string $fullName,
        bool $isActive,
        bool $isAdmin = false,
        bool $isOffice = false
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->fullName = $fullName;
        $this->isActive = $isActive;
        $this->isAdmin = $isAdmin;
        $this->isOffice = $isOffice;
        $this->refreshTokens = new ArrayCollection();
        $this->files = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    /**
     * @return Collection<int, RefreshToken>
     */
    public function getRefreshTokens(): Collection
    {
        return $this->refreshTokens;
    }

    /**
     * @return Collection<int, File>
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function getUserIdentifier(): string
    {
        // Email is always non-empty due to validation
        assert($this->email !== '');

        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->isAdmin) {
            $roles[] = 'ROLE_ADMIN';
        }

        if ($this->isOffice) {
            $roles[] = 'ROLE_OFFICE';
        }

        return $roles;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function setPassword(string $hashedPassword): self
    {
        $this->password = $hashedPassword;

        return $this;
    }

    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getIsOffice(): bool
    {
        return $this->isOffice;
    }

    public function setIsOffice(bool $isOffice): self
    {
        $this->isOffice = $isOffice;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }
}
