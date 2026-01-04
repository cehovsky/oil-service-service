<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class StorageContainerLocationCreateRequestDTO
{
    #[OA\Property(example: 'c2a63a4d-46b4-4d3b-98e3-d7343776e0b1')]
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private string $storageContainerId;

    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private string $warehouseId;

    #[OA\Property(example: '2026-01-04T00:00:00+00:00')]
    #[Assert\NotBlank]
    #[Assert\DateTime]
    private string $movedAt;

    public function getStorageContainerId(): string
    {
        return $this->storageContainerId;
    }

    public function setStorageContainerId(string $storageContainerId): self
    {
        $this->storageContainerId = $storageContainerId;

        return $this;
    }

    public function getWarehouseId(): string
    {
        return $this->warehouseId;
    }

    public function setWarehouseId(string $warehouseId): self
    {
        $this->warehouseId = $warehouseId;

        return $this;
    }

    public function getMovedAt(): string
    {
        return $this->movedAt;
    }

    public function setMovedAt(string $movedAt): self
    {
        $this->movedAt = $movedAt;

        return $this;
    }
}
