<?php

declare(strict_types=1);

namespace App\Warehouse;

use App\Auth\DBAL\Entity\User;
use App\Warehouse\DBAL\Entity\Recycling;
use App\Warehouse\DBAL\Entity\StorageContainer;
use App\Warehouse\DBAL\Entity\StorageContainerMaterial;
use App\Warehouse\DBAL\Repository\RecyclingRepository;
use App\Warehouse\DBAL\Repository\StorageContainerMaterialRepository;
use App\Warehouse\DBAL\Repository\StorageContainerRepository;
use App\Warehouse\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RecyclingService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageContainerRepository $storageContainerRepository,
        private readonly StorageContainerMaterialRepository $storageContainerMaterialRepository,
        private readonly RecyclingRepository $recyclingRepository,
    ) {
    }

    /**
     * @param string[]|null $storageContainerIds
     */
    public function createRecycling(
        ?DateTimeImmutable $recycledAt,
        ?User $recycledBy,
        User $actor,
        ?array $storageContainerIds = null,
    ): Recycling {
        $recycledBy ??= $recycledAt !== null ? $actor : null;

        $recycling = $this->entityFactory->createRecycling($recycledAt, $recycledBy);

        $this->syncStorageContainers($recycling, $storageContainerIds);

        $this->entityManager->persist($recycling);
        $this->entityManager->flush();

        return $recycling;
    }

    /**
     * @param string[]|null $storageContainerIds
     */
    public function updateRecycling(
        Recycling $recycling,
        ?DateTimeImmutable $recycledAt,
        ?User $recycledBy,
        User $actor,
        ?array $storageContainerIds = null,
    ): Recycling {
        $recycling->setRecycledAt($recycledAt);
        $recycling->setRecycledBy($recycledBy ?? ($recycledAt !== null ? ($recycling->getRecycledBy() ?? $actor) : $recycling->getRecycledBy()));
        $recycling->setUpdatedAt(new DateTimeImmutable());

        $this->syncStorageContainers($recycling, $storageContainerIds);

        $this->entityManager->flush();

        return $recycling;
    }

    public function deleteRecycling(Recycling $recycling): void
    {
        $this->entityManager->remove($recycling);
        $this->entityManager->flush();
    }

    public function recycle(Recycling $recycling, User $actor): void
    {
        $now = new DateTimeImmutable();

        if ($recycling->getRecycledAt() === null) {
            $recycling->setRecycledAt($now);
        }

        if ($recycling->getRecycledBy() === null) {
            $recycling->setRecycledBy($actor);
        }

        $recycling->setUpdatedAt($now);

        /** @var StorageContainer[] $storageContainers */
        $storageContainers = $recycling->getStorageContainers()->toArray();

        if ($storageContainers === []) {
            $this->entityManager->flush();

            return;
        }

        /** @var StorageContainerMaterial[] $materials */
        $materials = $this->storageContainerMaterialRepository->findBy([
            'storageContainer' => $storageContainers,
        ]);

        foreach ($storageContainers as $storageContainer) {
            $recycling->addStorageContainer($storageContainer);
        }

        foreach ($materials as $material) {
            $material->setIsRecycled(true);
            $material->setRecycling($recycling);
            $material->setUpdatedBy($actor);
            $material->setUpdatedAt($now);
            $recycling->addStorageContainerMaterial($material);
        }

        $this->entityManager->flush();
    }

    /**
     * @param string[]|null $storageContainerIds
     */
    private function syncStorageContainers(Recycling $recycling, ?array $storageContainerIds): void
    {
        if ($storageContainerIds === null) {
            return;
        }

        $storageContainerIds = array_values($storageContainerIds);

        $recycling->getStorageContainers()->clear();

        if ($storageContainerIds === []) {
            return;
        }

        $storageContainers = $this->storageContainerRepository->findBy([
            'id' => $storageContainerIds,
        ]);

        $foundIds = array_map(static fn (StorageContainer $container) => $container->getId()->__toString(), $storageContainers);

        foreach ($storageContainerIds as $storageContainerId) {
            if (!in_array($storageContainerId, $foundIds, true)) {
                throw new NotFoundHttpException('Storage container not found: ' . $storageContainerId);
            }
        }

        foreach ($storageContainers as $storageContainer) {
            $recycling->addStorageContainer($storageContainer);
        }
    }
}
