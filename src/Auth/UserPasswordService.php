<?php

declare(strict_types=1);

namespace App\Auth;

use App\Auth\DBAL\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class UserPasswordService
{
    public function __construct(
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {
    }

    public function hashPassword(string $plainPassword): string
    {
        return $this->passwordHasherFactory
            ->getPasswordHasher(User::class)
            ->hash($plainPassword);
    }
}
