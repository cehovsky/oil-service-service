<?php

// @phpcs:disable PSR1.Files.SideEffects

declare(strict_types=1);

namespace App\Domain\EntityManager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

final class EntityManagerGuardian
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ManagerRegistry $managerRegistry,
    ) {
    }

    public function reopenIfClosed(?string $entityManagerName = null): void
    {
        if ($this->entityManager->isOpen()) {
            return;
        }

        $this->managerRegistry->resetManager($entityManagerName);
    }

    public function clear(): void
    {
        $this->entityManager->clear();
    }
}
