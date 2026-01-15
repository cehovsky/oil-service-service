<?php

declare(strict_types=1);

namespace App\Warehouse;

use App\Auth\DBAL\Entity\User;
use App\Domain\Error\ErrorCollection;
use App\Domain\Error\ErrorItem;
use App\Domain\Exception\ValidationException;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Entity\Route;
use App\OilService\DBAL\Repository\OrderRepository;
use App\OilService\DBAL\Repository\RouteRepository;
use App\Warehouse\DBAL\Entity\Recycling;
use App\Warehouse\DBAL\Entity\StorageContainer;
use App\Warehouse\DBAL\Entity\StorageContainerMaterial;
use App\Warehouse\DBAL\Entity\Warehouse;
use App\Warehouse\DBAL\Entity\WasteMaterial;
use App\Warehouse\DBAL\Repository\RecyclingRepository;
use App\Warehouse\DBAL\Repository\StorageContainerMaterialRepository;
use App\Warehouse\DBAL\Repository\StorageContainerRepository;
use App\Warehouse\DBAL\Repository\WarehouseRepository;
use App\Warehouse\DBAL\Repository\WasteMaterialRepository;
use App\Warehouse\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StorageContainerMaterialService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageContainerRepository $storageContainerRepository,
        private readonly WasteMaterialRepository $wasteMaterialRepository,
        private readonly WarehouseRepository $warehouseRepository,
        private readonly RouteRepository $routeRepository,
        private readonly RecyclingRepository $recyclingRepository,
        private readonly OrderRepository $orderRepository,
        private readonly StorageContainerMaterialRepository $storageContainerMaterialRepository,
    ) {
    }

    public function createStorageContainerMaterial(
        string $storageContainerId,
        string $wasteMaterialId,
        float $volume,
        bool $isRecycled,
        User $actor,
        ?string $warehouseId = null,
        ?string $routeId = null,
        ?string $recyclingId = null,
        ?string $orderId = null,
    ): StorageContainerMaterial {
        $this->assertSingleOrigin($warehouseId, $routeId);

        $storageContainer = $this->getStorageContainer($storageContainerId);
        $wasteMaterial = $this->getWasteMaterial($wasteMaterialId);
        $warehouse = $warehouseId !== null ? $this->getWarehouse($warehouseId) : null;
        $route = $routeId !== null ? $this->getRoute($routeId) : null;
        $recycling = $recyclingId !== null ? $this->getRecycling($recyclingId) : null;
        $order = $orderId !== null ? $this->getOrder($orderId) : null;

        $this->assertWasteMaterialPreferred($storageContainer, $wasteMaterial);

        $material = $this->entityFactory->createStorageContainerMaterial(
            $storageContainer,
            $wasteMaterial,
            $volume,
            $isRecycled,
            $actor,
            $actor,
            $warehouse,
            $route,
            $recycling,
            $order,
        );

        if ($recycling !== null) {
            $recycling->addStorageContainer($storageContainer);
        }

        $this->entityManager->persist($material);
        $this->entityManager->flush();

        return $material;
    }

    public function updateStorageContainerMaterial(
        StorageContainerMaterial $storageContainerMaterial,
        string $storageContainerId,
        string $wasteMaterialId,
        float $volume,
        bool $isRecycled,
        User $actor,
        ?string $warehouseId = null,
        ?string $routeId = null,
        ?string $recyclingId = null,
        ?string $orderId = null,
    ): StorageContainerMaterial {
        $this->assertSingleOrigin($warehouseId, $routeId);

        $previousRecycling = $storageContainerMaterial->getRecycling();
        $previousStorageContainer = $storageContainerMaterial->getStorageContainer();

        $storageContainer = $this->getStorageContainer($storageContainerId);
        $wasteMaterial = $this->getWasteMaterial($wasteMaterialId);
        $warehouse = $warehouseId !== null ? $this->getWarehouse($warehouseId) : null;
        $route = $routeId !== null ? $this->getRoute($routeId) : null;
        $recycling = $recyclingId !== null ? $this->getRecycling($recyclingId) : null;
        $order = $orderId !== null ? $this->getOrder($orderId) : null;

        $this->assertWasteMaterialPreferred($storageContainer, $wasteMaterial);

        $storageContainerChanged = $previousStorageContainer->getId()->__toString() !== $storageContainer->getId()->__toString();

        $storageContainerMaterial->setStorageContainer($storageContainer);
        $storageContainerMaterial->setWasteMaterial($wasteMaterial);
        $storageContainerMaterial->setWarehouse($warehouse);
        $storageContainerMaterial->setRoute($route);
        $storageContainerMaterial->setRecycling($recycling);
        $storageContainerMaterial->setOrder($order);
        $storageContainerMaterial->setVolume($volume);
        $storageContainerMaterial->setIsRecycled($isRecycled);
        $storageContainerMaterial->setUpdatedBy($actor);
        $storageContainerMaterial->setUpdatedAt(new DateTimeImmutable());

        if ($storageContainerChanged) {
            $history = $this->entityFactory->createStorageContainerMaterialHistory(
                $storageContainerMaterial,
                $storageContainer,
                $actor,
            );

            $storageContainerMaterial->addHistory($history);
            $this->entityManager->persist($history);
        }

        if ($recycling !== null) {
            $recycling->addStorageContainer($storageContainer);
        }

        if ($previousRecycling !== null && $previousRecycling !== $recycling) {
            $this->detachContainerFromRecyclingIfUnused($previousRecycling, $previousStorageContainer, $storageContainerMaterial);
        }

        if ($storageContainerChanged && $recycling !== null && $previousRecycling === $recycling) {
            $this->detachContainerFromRecyclingIfUnused($recycling, $previousStorageContainer, $storageContainerMaterial);
        }

        $this->entityManager->flush();

        return $storageContainerMaterial;
    }

    /**
     * Move all non-recycled materials from one storage container to another.
     * Returns moved materials (can be empty if none matched the criteria).
     *
     * @return StorageContainerMaterial[]
     */
    public function moveNonRecycledMaterials(
        string $sourceStorageContainerId,
        string $targetStorageContainerId,
        User $actor,
    ): array {
        if ($sourceStorageContainerId === $targetStorageContainerId) {
            $errorCollection = new ErrorCollection();
            $errorCollection->add(
                new ErrorItem(
                    'Source and target storage container must differ.',
                    'storageContainerSame',
                    'targetStorageContainerId',
                )
            );

            throw new ValidationException(errorCollection: $errorCollection);
        }

        $source = $this->getStorageContainer($sourceStorageContainerId);
        $target = $this->getStorageContainer($targetStorageContainerId);

        $materials = $this->storageContainerMaterialRepository->findBy([
            'storageContainer' => $source,
            'isRecycled' => false,
        ]);

        $moved = [];

        foreach ($materials as $material) {
            $moved[] = $this->updateStorageContainerMaterial(
                $material,
                $targetStorageContainerId,
                $material->getWasteMaterial()->getId()->__toString(),
                $material->getVolume(),
                $material->getIsRecycled(),
                $actor,
                $material->getWarehouse()?->getId()->__toString(),
                $material->getRoute()?->getId()->__toString(),
                $material->getRecycling()?->getId()->__toString(),
                $material->getOrder()?->getId()->__toString(),
            );
        }

        return $moved;
    }

    /**
     * Move selected materials to a target storage container.
     * Only non-recycled materials are accepted.
     * Returns moved materials.
     *
     * @param string[] $storageContainerMaterialIds
     * @return StorageContainerMaterial[]
     */
    public function moveSelectedMaterials(
        string $targetStorageContainerId,
        array $storageContainerMaterialIds,
        User $actor,
    ): array {
        if ($storageContainerMaterialIds === []) {
            $errorCollection = new ErrorCollection();
            $errorCollection->add(
                new ErrorItem(
                    'At least one storage container material ID must be provided.',
                    'storageContainerMaterialIdsEmpty',
                    'storageContainerMaterialIds',
                )
            );

            throw new ValidationException(errorCollection: $errorCollection);
        }

        $target = $this->getStorageContainer($targetStorageContainerId);

        $materials = $this->storageContainerMaterialRepository->findBy([
            'id' => $storageContainerMaterialIds,
        ]);

        if (count($materials) !== count($storageContainerMaterialIds)) {
            $errorCollection = new ErrorCollection();
            $errorCollection->add(
                new ErrorItem(
                    'Some storage container materials were not found.',
                    'storageContainerMaterialNotFound',
                    'storageContainerMaterialIds',
                )
            );

            throw new ValidationException(errorCollection: $errorCollection);
        }

        foreach ($materials as $material) {
            if ($material->getIsRecycled()) {
                $errorCollection = new ErrorCollection();
                $errorCollection->add(
                    new ErrorItem(
                        'Recycled materials cannot be moved.',
                        'storageContainerMaterialIsRecycled',
                        'storageContainerMaterialIds',
                    )
                );

                throw new ValidationException(errorCollection: $errorCollection);
            }
        }

        $moved = [];

        foreach ($materials as $material) {
            $moved[] = $this->updateStorageContainerMaterial(
                $material,
                $targetStorageContainerId,
                $material->getWasteMaterial()->getId()->__toString(),
                $material->getVolume(),
                $material->getIsRecycled(),
                $actor,
                $material->getWarehouse()?->getId()->__toString(),
                $material->getRoute()?->getId()->__toString(),
                $material->getRecycling()?->getId()->__toString(),
                $material->getOrder()?->getId()->__toString(),
            );
        }

        return $moved;
    }

    public function deleteStorageContainerMaterial(StorageContainerMaterial $storageContainerMaterial): void
    {
        $recycling = $storageContainerMaterial->getRecycling();
        $storageContainer = $storageContainerMaterial->getStorageContainer();

        $this->entityManager->remove($storageContainerMaterial);
        $this->entityManager->flush();

        if ($recycling !== null) {
            $this->detachContainerFromRecyclingIfUnused($recycling, $storageContainer, $storageContainerMaterial);
            $this->entityManager->flush();
        }
    }

    private function getStorageContainer(string $storageContainerId): StorageContainer
    {
        $storageContainer = $this->storageContainerRepository->find($storageContainerId);

        if ($storageContainer === null) {
            throw new NotFoundHttpException('Storage container not found');
        }

        return $storageContainer;
    }

    private function getWasteMaterial(string $wasteMaterialId): WasteMaterial
    {
        $wasteMaterial = $this->wasteMaterialRepository->find($wasteMaterialId);

        if ($wasteMaterial === null) {
            throw new NotFoundHttpException('Waste material not found');
        }

        return $wasteMaterial;
    }

    private function getWarehouse(string $warehouseId): Warehouse
    {
        $warehouse = $this->warehouseRepository->find($warehouseId);

        if ($warehouse === null) {
            throw new NotFoundHttpException('Warehouse not found');
        }

        return $warehouse;
    }

    private function getRoute(string $routeId): Route
    {
        $route = $this->routeRepository->find($routeId);

        if ($route === null) {
            throw new NotFoundHttpException('Route not found');
        }

        return $route;
    }

    private function getRecycling(string $recyclingId): Recycling
    {
        $recycling = $this->recyclingRepository->find($recyclingId);

        if ($recycling === null) {
            throw new NotFoundHttpException('Recycling not found');
        }

        return $recycling;
    }

    private function getOrder(string $orderId): Order
    {
        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            throw new NotFoundHttpException('Order not found');
        }

        return $order;
    }

    private function assertWasteMaterialPreferred(StorageContainer $storageContainer, WasteMaterial $wasteMaterial): void
    {
        if ($storageContainer->getPreferredWasteMaterials()->contains($wasteMaterial)) {
            return;
        }

        $errorCollection = new ErrorCollection();
        $errorCollection->add(
            new ErrorItem(
                'Waste material is not preferred for this storage container.',
                'wasteMaterialNotPreferred',
                'wasteMaterialId',
            )
        );

        throw new ValidationException(errorCollection: $errorCollection);
    }

    private function assertSingleOrigin(?string $warehouseId, ?string $routeId): void
    {
        if ($warehouseId !== null && $routeId !== null) {
            $errorCollection = new ErrorCollection();
            $errorCollection->add(
                new ErrorItem(
                    'Provide at most one of warehouseId or routeId.',
                    'multipleOrigins',
                    'routeId',
                )
            );

            throw new ValidationException(errorCollection: $errorCollection);
        }
    }

    private function detachContainerFromRecyclingIfUnused(
        Recycling $recycling,
        StorageContainer $storageContainer,
        StorageContainerMaterial $ignoredMaterial,
    ): void {
        $usageCount = $this->storageContainerMaterialRepository->count([
            'recycling' => $recycling,
            'storageContainer' => $storageContainer,
        ]);

        if ($ignoredMaterial->getRecycling() === $recycling && $ignoredMaterial->getStorageContainer() === $storageContainer) {
            $usageCount -= 1;
        }

        if ($usageCount <= 0) {
            $recycling->removeStorageContainer($storageContainer);
        }
    }
}
