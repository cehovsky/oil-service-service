<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class WasteMaterialDetailDTO
{
    #[OA\Property(ref: new Model(type: WasteMaterialDTO::class))]
    private WasteMaterialDTO $wasteMaterial;

    /**
     * @var StorageContainerSummaryDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: StorageContainerSummaryDTO::class)))]
    private array $storageContainers;

    /**
     * @param StorageContainerSummaryDTO[] $storageContainers
     */
    public function __construct(WasteMaterialDTO $wasteMaterial, array $storageContainers)
    {
        $this->wasteMaterial = $wasteMaterial;
        $this->storageContainers = $storageContainers;
    }

    public function getWasteMaterial(): WasteMaterialDTO
    {
        return $this->wasteMaterial;
    }

    /**
     * @return StorageContainerSummaryDTO[]
     */
    public function getStorageContainers(): array
    {
        return $this->storageContainers;
    }
}
