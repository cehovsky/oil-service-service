<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class StorageContainerMaterialMoveSelectedRequestDTO
{
    /**
     * @var string[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string', example: '2aab3a6d-1bde-47b2-8f12-a6120f8470c0'))]
    #[Assert\NotNull]
    #[Assert\Count(min: 1)]
    #[Assert\All([
        new Assert\Uuid(),
    ])]
    private array $storageContainerMaterialIds = [];

    /**
     * @return string[]
     */
    public function getStorageContainerMaterialIds(): array
    {
        return $this->storageContainerMaterialIds;
    }

    /**
     * @param string[] $storageContainerMaterialIds
     */
    public function setStorageContainerMaterialIds(array $storageContainerMaterialIds): self
    {
        $this->storageContainerMaterialIds = $storageContainerMaterialIds;

        return $this;
    }
}
