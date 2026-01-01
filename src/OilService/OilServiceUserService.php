<?php

declare(strict_types=1);

namespace App\OilService;

use App\OilService\DBAL\Entity\User;
use App\OilService\Factory\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;

class OilServiceUserService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createUser(string $email, string $phone, string $fullName): User
    {
        $user = $this->entityFactory->createUser($email, $phone, $fullName);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(User $user, string $email, string $phone, string $fullName): User
    {
        $user->setEmail($email);
        $user->setPhone($phone);
        $user->setFullName($fullName);

        $this->entityManager->flush();

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}
