<?php

declare(strict_types=1);

namespace App\Modules\Users\DTO;

use OpenApi\Attributes as OA;

class UserDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'admin@example.com')]
    private string $email;

    #[OA\Property(example: 'Admin User')]
    private string $fullName;

    #[OA\Property(example: true)]
    private bool $isActive;

    #[OA\Property(example: true)]
    private bool $isAdmin;

    #[OA\Property(example: false)]
    private bool $isOffice;

    public function __construct(
        string $id,
        string $email,
        string $fullName,
        bool $isActive,
        bool $isAdmin,
        bool $isOffice
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->fullName = $fullName;
        $this->isActive = $isActive;
        $this->isAdmin = $isAdmin;
        $this->isOffice = $isOffice;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
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
