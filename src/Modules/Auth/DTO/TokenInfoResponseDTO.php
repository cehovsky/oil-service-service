<?php

declare(strict_types=1);

namespace App\Modules\Auth\DTO;

class TokenInfoResponseDTO
{
    public function __construct(
        private readonly string $id,
        private readonly string $email,
        private readonly string $fullName,
        private readonly bool $isAdmin,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }
}
