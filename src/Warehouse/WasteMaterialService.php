<?php

declare(strict_types=1);

namespace App\Warehouse;

use App\Domain\Error\ErrorCollection;
use App\Domain\Error\ErrorItem;
use App\Domain\Exception\ValidationException;
use App\Warehouse\DBAL\Entity\WasteMaterial;
use App\Warehouse\DBAL\Enum\VolumeUnitEnum;
use App\Warehouse\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class WasteMaterialService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
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
        $wasteMaterial = $this->entityFactory->createWasteMaterial(
            $code,
            $label,
            $shortLabel,
            $isActive,
            $volumeUnit,
            $catalogDescription,
        );

        $this->entityManager->persist($wasteMaterial);
        $this->entityManager->flush();

        return $wasteMaterial;
    }

    public function updateWasteMaterial(
        WasteMaterial $wasteMaterial,
        string $code,
        string $label,
        string $shortLabel,
        bool $isActive,
        VolumeUnitEnum $volumeUnit,
        ?string $catalogDescription = null,
    ): WasteMaterial {
        $wasteMaterial->setCode($code);
        $wasteMaterial->setLabel($label);
        $wasteMaterial->setShortLabel($shortLabel);
        $wasteMaterial->setIsActive($isActive);
        $wasteMaterial->setVolumeUnit($volumeUnit);
        $wasteMaterial->setCatalogDescription($catalogDescription);
        $wasteMaterial->setUpdatedAt(new DateTimeImmutable());

        $this->entityManager->flush();

        return $wasteMaterial;
    }

    public function deleteWasteMaterial(WasteMaterial $wasteMaterial): void
    {
        if ($wasteMaterial->getPreferredStorageContainers()->count() > 0) {
            throw $this->createWasteMaterialInUseException();
        }

        $this->entityManager->remove($wasteMaterial);
        $this->entityManager->flush();
    }

    private function createWasteMaterialInUseException(): ValidationException
    {
        $errorCollection = new ErrorCollection();
        $errorCollection->add(
            new ErrorItem(
                'Waste material is already used by storage containers. Deactivate it instead of deleting.',
                'wasteMaterialInUse',
                null,
            )
        );

        return new ValidationException(errorCollection: $errorCollection);
    }
}
