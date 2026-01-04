<?php

declare(strict_types=1);

namespace App\Warehouse;

use App\Domain\Error\ErrorCollection;
use App\Domain\Error\ErrorItem;
use App\Domain\Exception\ValidationException;
use App\Warehouse\DBAL\Entity\StorageContainer;
use App\Warehouse\DBAL\Enum\StorageContainerTypeEnum;
use App\Warehouse\DBAL\Enum\VolumeUnitEnum;
use App\Warehouse\DBAL\Repository\WasteMaterialRepository;
use App\Warehouse\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StorageContainerService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly WasteMaterialRepository $wasteMaterialRepository,
    ) {
    }

    /**
     * @param string[]|null $preferredWasteMaterialIds
     */
    public function createStorageContainer(
        string $code,
        StorageContainerTypeEnum $type,
        float $capacity,
        VolumeUnitEnum $volumeUnit,
        bool $isActive,
        ?string $description = null,
        ?array $preferredWasteMaterialIds = null,
    ): StorageContainer {
        $storageContainer = $this->entityFactory->createStorageContainer(
            $code,
            $type,
            $capacity,
            $volumeUnit,
            $isActive,
            $description,
        );

        $this->syncPreferredWasteMaterials($storageContainer, $preferredWasteMaterialIds);

        $this->entityManager->persist($storageContainer);
        $this->entityManager->flush();

        return $storageContainer;
    }

    /**
     * @param string[]|null $preferredWasteMaterialIds
     */
    public function updateStorageContainer(
        StorageContainer $storageContainer,
        string $code,
        StorageContainerTypeEnum $type,
        float $capacity,
        VolumeUnitEnum $volumeUnit,
        bool $isActive,
        ?string $description = null,
        ?array $preferredWasteMaterialIds = null,
    ): StorageContainer {
        $storageContainer->setCode($code);
        $storageContainer->setType($type);
        $storageContainer->setCapacity($capacity);
        $storageContainer->setVolumeUnit($volumeUnit);
        $storageContainer->setIsActive($isActive);
        $storageContainer->setDescription($description);
        $storageContainer->setUpdatedAt(new DateTimeImmutable());

        $this->syncPreferredWasteMaterials($storageContainer, $preferredWasteMaterialIds);

        $this->entityManager->flush();

        return $storageContainer;
    }

    public function deleteStorageContainer(StorageContainer $storageContainer): void
    {
        if ($storageContainer->getLocations()->count() > 0 || $storageContainer->getPreferredWasteMaterials()->count() > 0) {
            throw $this->createStorageContainerInUseException();
        }

        $this->entityManager->remove($storageContainer);
        $this->entityManager->flush();
    }

    /**
     * @param string[]|null $preferredWasteMaterialIds
     */
    private function syncPreferredWasteMaterials(StorageContainer $storageContainer, ?array $preferredWasteMaterialIds): void
    {
        if ($preferredWasteMaterialIds === null) {
            return;
        }

        foreach ($storageContainer->getPreferredWasteMaterials()->toArray() as $preferredWasteMaterial) {
            $storageContainer->removePreferredWasteMaterial($preferredWasteMaterial);
        }

        if ($preferredWasteMaterialIds === []) {
            return;
        }

        $preferredWasteMaterials = $this->wasteMaterialRepository->findBy([
            'id' => $preferredWasteMaterialIds,
        ]);

        $foundIds = array_map(static fn ($material) => $material->getId()->__toString(), $preferredWasteMaterials);

        foreach ($preferredWasteMaterialIds as $preferredWasteMaterialId) {
            if (!in_array($preferredWasteMaterialId, $foundIds, true)) {
                throw new NotFoundHttpException('Waste material not found: ' . $preferredWasteMaterialId);
            }
        }

        foreach ($preferredWasteMaterials as $preferredWasteMaterial) {
            $storageContainer->addPreferredWasteMaterial($preferredWasteMaterial);
        }
    }

    private function createStorageContainerInUseException(): ValidationException
    {
        $errorCollection = new ErrorCollection();
        $errorCollection->add(
            new ErrorItem(
                'Storage container is in use. Deactivate it instead of deleting.',
                'storageContainerInUse',
                null,
            )
        );

        return new ValidationException(errorCollection: $errorCollection);
    }
}
