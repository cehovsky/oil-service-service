<?php

declare(strict_types=1);

namespace App\Warehouse\Factory;

use App\Warehouse\DBAL\Entity\StorageContainer;
use App\Warehouse\DBAL\Entity\WasteMaterial;
use App\Warehouse\DBAL\Enum\StorageContainerTypeEnum;
use App\Warehouse\DBAL\Enum\StorageVolumeUnitEnum;
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
    ): WasteMaterial {
        $now = new DateTimeImmutable();

        return new WasteMaterial(
            $this->uuidFactory->timeBased()->create(),
            $code,
            $label,
            $shortLabel,
            $isActive,
            $now,
            $now,
        );
    }

    public function createStorageContainer(
        string $code,
        StorageContainerTypeEnum $type,
        float $capacity,
        StorageVolumeUnitEnum $volumeUnit,
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
}
