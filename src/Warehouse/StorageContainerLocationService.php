<?php

declare(strict_types=1);

namespace App\Warehouse;

use App\Domain\Error\ErrorCollection;
use App\Domain\Error\ErrorItem;
use App\Domain\Exception\ValidationException;
use App\OilService\DBAL\Entity\Route;
use App\OilService\DBAL\Repository\RouteRepository;
use App\Warehouse\DBAL\Entity\StorageContainer;
use App\Warehouse\DBAL\Entity\StorageContainerLocation;
use App\Warehouse\DBAL\Entity\Warehouse;
use App\Warehouse\DBAL\Repository\StorageContainerRepository;
use App\Warehouse\DBAL\Repository\WarehouseRepository;
use App\Warehouse\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StorageContainerLocationService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageContainerRepository $storageContainerRepository,
        private readonly WarehouseRepository $warehouseRepository,
        private readonly RouteRepository $routeRepository,
    ) {
    }

    public function createStorageContainerLocation(
        string $storageContainerId,
        ?string $warehouseId,
        ?string $routeId,
        DateTimeImmutable $movedAt,
    ): StorageContainerLocation {
        $this->assertSingleLocationTarget($warehouseId, $routeId);

        $storageContainer = $this->getStorageContainer($storageContainerId);
        $warehouse = $warehouseId !== null ? $this->getWarehouse($warehouseId) : null;
        $route = $routeId !== null ? $this->getRoute($routeId) : null;

        $location = $this->entityFactory->createStorageContainerLocation(
            $storageContainer,
            $movedAt,
            $warehouse,
            $route,
        );

        $storageContainer->addLocation($location);

        if ($warehouse !== null) {
            $warehouse->addStorageContainerLocation($location);
        }

        if ($route !== null) {
            $route->addStorageContainerLocation($location);
        }

        $this->entityManager->persist($location);
        $this->entityManager->flush();

        return $location;
    }

    public function updateStorageContainerLocation(
        StorageContainerLocation $storageContainerLocation,
        string $storageContainerId,
        ?string $warehouseId,
        ?string $routeId,
        DateTimeImmutable $movedAt,
    ): StorageContainerLocation {
        $this->assertSingleLocationTarget($warehouseId, $routeId);

        $storageContainer = $this->getStorageContainer($storageContainerId);
        $warehouse = $warehouseId !== null ? $this->getWarehouse($warehouseId) : null;
        $route = $routeId !== null ? $this->getRoute($routeId) : null;

        $storageContainerLocation->setStorageContainer($storageContainer);

        if ($warehouse !== null) {
            $storageContainerLocation->setWarehouse($warehouse);
        }

        if ($route !== null) {
            $storageContainerLocation->setRoute($route);
        }

        $storageContainerLocation->setMovedAt($movedAt);
        $storageContainerLocation->setUpdatedAt(new DateTimeImmutable());

        $this->entityManager->flush();

        return $storageContainerLocation;
    }

    public function deleteStorageContainerLocation(StorageContainerLocation $storageContainerLocation): void
    {
        $this->entityManager->remove($storageContainerLocation);
        $this->entityManager->flush();
    }

    private function assertSingleLocationTarget(?string $warehouseId, ?string $routeId): void
    {
        $onlyWarehouseSet = $warehouseId !== null && $routeId === null;
        $onlyRouteSet = $routeId !== null && $warehouseId === null;

        if ($onlyWarehouseSet || $onlyRouteSet) {
            return;
        }

        $errorCollection = new ErrorCollection();
        $errorCollection->add(
            new ErrorItem(
                'Exactly one of warehouseId or routeId must be provided.',
                'invalidLocationTarget',
                'locationTarget',
            )
        );

        throw new ValidationException(errorCollection: $errorCollection);
    }

    private function getStorageContainer(string $storageContainerId): StorageContainer
    {
        $storageContainer = $this->storageContainerRepository->find($storageContainerId);

        if ($storageContainer === null) {
            throw new NotFoundHttpException('Storage container not found');
        }

        return $storageContainer;
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
}
