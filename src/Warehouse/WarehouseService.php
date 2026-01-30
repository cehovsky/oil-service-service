<?php

declare(strict_types=1);

namespace App\Warehouse;

use App\Warehouse\DBAL\Entity\Warehouse;
use App\Warehouse\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class WarehouseService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
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
        $warehouse = $this->entityFactory->createWarehouse(
            $label,
            $shortLabel,
            $isActive,
            $address,
            $latitude,
            $longitude,
            $isGarage,
        );

        $this->entityManager->persist($warehouse);
        $this->entityManager->flush();

        return $warehouse;
    }

    public function updateWarehouse(
        Warehouse $warehouse,
        string $label,
        string $shortLabel,
        bool $isActive,
        ?string $address = null,
        ?float $latitude = null,
        ?float $longitude = null,
        bool $isGarage = false,
    ): Warehouse {
        $warehouse->setLabel($label);
        $warehouse->setShortLabel($shortLabel);
        $warehouse->setIsActive($isActive);
        $warehouse->setAddress($address);
        $warehouse->setLatitude($latitude);
        $warehouse->setLongitude($longitude);
        $warehouse->setIsGarage($isGarage);
        $warehouse->setUpdatedAt(new DateTimeImmutable());

        $this->entityManager->flush();

        return $warehouse;
    }

    public function deleteWarehouse(Warehouse $warehouse): void
    {
        $this->entityManager->remove($warehouse);
        $this->entityManager->flush();
    }
}
