<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Modules\Warehouse\Validation\Constraint\PreferredWasteMaterialForStorageContainer;
use App\Modules\Warehouse\Validation\Constraint\StorageContainerMaterialOriginChoice;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[PreferredWasteMaterialForStorageContainer]
#[StorageContainerMaterialOriginChoice]
class StorageContainerMaterialCreateRequestDTO
{
    #[OA\Property(example: '2aab3a6d-1bde-47b2-8f12-a6120f8470c0')]
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private string $storageContainerId;

    #[OA\Property(example: 'f1b77f2d-390a-4f3a-b0d4-21c922132e10')]
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private string $wasteMaterialId;

    #[OA\Property(example: 120.5)]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private float $volume;

    #[OA\Property(example: false)]
    #[Assert\NotNull]
    private bool $isRecycled;

    #[OA\Property(example: '7f3e6f8c-6a13-4a3d-b701-34d87d2c8c5a', nullable: true)]
    #[Assert\Uuid]
    private ?string $warehouseId = null;

    #[OA\Property(example: '5f6e6b52-4b4e-4a1f-8f58-4b8b6cb9b641', nullable: true)]
    #[Assert\Uuid]
    private ?string $routeId = null;

    #[OA\Property(example: 'fd521c70-9437-4cf5-9fb6-a88dd4923ecf', nullable: true)]
    #[Assert\Uuid]
    private ?string $recyclingId = null;

    #[OA\Property(example: '4c4b9415-4d8c-4c2e-b042-1e0ce1c1932a', nullable: true)]
    #[Assert\Uuid]
    private ?string $orderId = null;

    public function getStorageContainerId(): string
    {
        return $this->storageContainerId;
    }

    public function setStorageContainerId(string $storageContainerId): self
    {
        $this->storageContainerId = $storageContainerId;

        return $this;
    }

    public function getWasteMaterialId(): string
    {
        return $this->wasteMaterialId;
    }

    public function setWasteMaterialId(string $wasteMaterialId): self
    {
        $this->wasteMaterialId = $wasteMaterialId;

        return $this;
    }

    public function getVolume(): float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getIsRecycled(): bool
    {
        return $this->isRecycled;
    }

    public function setIsRecycled(bool $isRecycled): self
    {
        $this->isRecycled = $isRecycled;

        return $this;
    }

    public function getWarehouseId(): ?string
    {
        return $this->warehouseId;
    }

    public function setWarehouseId(?string $warehouseId): self
    {
        $this->warehouseId = $warehouseId;

        return $this;
    }

    public function getRouteId(): ?string
    {
        return $this->routeId;
    }

    public function setRouteId(?string $routeId): self
    {
        $this->routeId = $routeId;

        return $this;
    }

    public function getRecyclingId(): ?string
    {
        return $this->recyclingId;
    }

    public function setRecyclingId(?string $recyclingId): self
    {
        $this->recyclingId = $recyclingId;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(?string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }
}
