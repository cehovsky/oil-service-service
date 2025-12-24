<?php

declare(strict_types=1);

namespace App\Modules\Auth\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AuthenticateRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        private readonly string $email = '',
        #[Assert\NotBlank]
        private readonly string $password = '',
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
