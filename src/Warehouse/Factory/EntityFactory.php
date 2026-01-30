<?php

declare(strict_types=1);

namespace App\Warehouse\Factory;

use App\Auth\DBAL\Entity\User;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Entity\Route;
use App\Warehouse\DBAL\Entity\Recycling;
use App\Warehouse\DBAL\Entity\StorageContainer;
use App\Warehouse\DBAL\Entity\StorageContainerLocation;
use App\Warehouse\DBAL\Entity\StorageContainerMaterial;
use App\Warehouse\DBAL\Entity\StorageContainerMaterialHistory;
use App\Warehouse\DBAL\Entity\WasteMaterial;
use App\Warehouse\DBAL\Entity\Warehouse;
use App\Warehouse\DBAL\Enum\StorageContainerTypeEnum;
use App\Warehouse\DBAL\Enum\VolumeUnitEnum;
use DateTimeImmutable;
use Symfony\Component\Uid\Factory\UuidFactory;

class EntityFactory
{
    public function __construct(
        private readonly UuidFactory $uuidFactory,
    ) {
    }

    public function createWasteMaterial(
        string $code,
        string $label,
        string $shortLabel,
        bool $isActive,
        VolumeUnitEnum $volumeUnit,
        ?string $catalogDescription = null,
    ): WasteMaterial {
        $now = new DateTimeImmutable();

        return new WasteMaterial(
            $this->uuidFactory->timeBased()->create(),
            $code,
            $label,
            $shortLabel,
            $isActive,
            $volumeUnit,
            $catalogDescription,
            $now,
            $now,
        );
    }

    public function createStorageContainer(
        string $code,
        StorageContainerTypeEnum $type,
        float $capacity,
        VolumeUnitEnum $volumeUnit,
        bool $isActive,
        ?string $description = null,
    ): StorageContainer {
        $now = new DateTimeImmutable();

        return new StorageContainer(
            $this->uuidFactory->timeBased()->create(),
            $code,
            $description,
            $isActive,
            $type,
            $capacity,
            $volumeUnit,
            $now,
            $now,
        );
    }

    public function createWarehouse(
        string $label,
        string $shortLabel,
        bool $isActive,
        ?string $address = null,
        ?float $latitude = null,
        ?float $longitude = null,
        bool $isGarage = false,
    ): Warehouse {
        $now = new DateTimeImmutable();

        return new Warehouse(
            $this->uuidFactory->timeBased()->create(),
            $label,
            $shortLabel,
            $address,
            $isActive,
            $latitude,
            $longitude,
            $isGarage,
            $now,
            $now,
        );
    }

    public function createStorageContainerLocation(
        StorageContainer $storageContainer,
        DateTimeImmutable $movedAt,
        ?Warehouse $warehouse,
        ?Route $route,
    ): StorageContainerLocation {
        $now = new DateTimeImmutable();

        return new StorageContainerLocation(
            $this->uuidFactory->timeBased()->create(),
            $storageContainer,
            $movedAt,
            $warehouse,
            $route,
            $now,
            $now,
        );
    }

    public function createStorageContainerMaterial(
        StorageContainer $storageContainer,
        WasteMaterial $wasteMaterial,
        float $volume,
        bool $isRecycled,
        User $createdBy,
        User $updatedBy,
        ?Warehouse $warehouse,
        ?Route $route,
        ?Recycling $recycling = null,
        ?Order $order = null,
    ): StorageContainerMaterial {
        $now = new DateTimeImmutable();

        $material = new StorageContainerMaterial(
            $this->uuidFactory->timeBased()->create(),
            $storageContainer,
            $wasteMaterial,
            $volume,
            $isRecycled,
            $createdBy,
            $updatedBy,
            $now,
            $now,
            $warehouse,
            $route,
            $recycling,
            $order,
        );

        $history = $this->createStorageContainerMaterialHistory(
            $material,
            $storageContainer,
            $createdBy,
            $now,
        );

        $material->addHistory($history);

        if ($recycling !== null) {
            $recycling->addStorageContainerMaterial($material);
            $recycling->addStorageContainer($storageContainer);
        }

        return $material;
    }

    public function createStorageContainerMaterialHistory(
        StorageContainerMaterial $storageContainerMaterial,
        StorageContainer $storageContainer,
        User $createdBy,
        ?DateTimeImmutable $createdAt = null,
    ): StorageContainerMaterialHistory {
        $createdAt ??= new DateTimeImmutable();

        return new StorageContainerMaterialHistory(
            $this->uuidFactory->timeBased()->create(),
            $storageContainerMaterial,
            $storageContainer,
            $createdBy,
            $createdAt,
        );
    }

    public function createRecycling(
        ?DateTimeImmutable $recycledAt = null,
        ?User $recycledBy = null,
    ): Recycling {
        $now = new DateTimeImmutable();

        return new Recycling(
            $this->uuidFactory->timeBased()->create(),
            $recycledAt,
            $recycledBy,
            $now,
            $now,
        );
    }
}
