<?php

declare(strict_types=1);

namespace App\Modules\Users\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class UsersUpdateRequestDTO
{
    #[OA\Property(example: 'user@example.com')]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private string $email;

    #[OA\Property(example: 'NewPassword123!')]
    #[Assert\Length(min: 8, max: 255)]
    #[Assert\NotBlank]
    private ?string $password = null;

    #[OA\Property(example: 'User Name')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $fullName;

    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isActive;

    #[OA\Property(example: false)]
    #[Assert\NotNull]
    private bool $isAdmin;

    #[OA\Property(example: false)]
    #[Assert\NotNull]
    private bool $isOffice;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
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
}
