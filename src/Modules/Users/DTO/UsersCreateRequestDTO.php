<?php

declare(strict_types=1);

namespace App\Modules\Users\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class UsersCreateRequestDTO
{
    #[OA\Property(example: 'admin@example.com')]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private string $email;

    #[OA\Property(example: 'StrongPassword123!')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 255)]
    private string $password;

    #[OA\Property(example: 'Admin User')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $fullName;

    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isActive;

    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isAdmin;

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

    public function setPassword(string $password): self
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
}
