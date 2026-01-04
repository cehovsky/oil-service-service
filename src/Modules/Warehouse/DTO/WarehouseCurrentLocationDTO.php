<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class WarehouseCurrentLocationDTO
{
    #[OA\Property(ref: new Model(type: StorageContainerLocationBasicDTO::class))]
    private StorageContainerLocationBasicDTO $location;

    #[OA\Property(ref: new Model(type: StorageContainerSummaryDTO::class))]
    private StorageContainerSummaryDTO $storageContainer;

    public function __construct(
        StorageContainerLocationBasicDTO $location,
        StorageContainerSummaryDTO $storageContainer
    ) {
        $this->location = $location;
        $this->storageContainer = $storageContainer;
    }

    public function getLocation(): StorageContainerLocationBasicDTO
    {
        return $this->location;
    }

    public function getStorageContainer(): StorageContainerSummaryDTO
    {
        return $this->storageContainer;
    }
}
