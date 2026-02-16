<?php

declare(strict_types=1);

namespace App\Modules\CarApp\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class OrderMaterialsUpdateItemDTO
{
    #[OA\Property(example: '2aab3a6d-1bde-47b2-8f12-a6120f8470c0', nullable: true)]
    #[Assert\Uuid]
    private ?string $materialId = null;

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

    public function getMaterialId(): ?string
    {
        return $this->materialId;
    }

    public function setMaterialId(?string $materialId): self
    {
        $this->materialId = $materialId;

        return $this;
    }

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
}
