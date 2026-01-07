<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class StorageContainerMaterialSummaryDTO
{
    #[OA\Property(example: '2aab3a6d-1bde-47b2-8f12-a6120f8470c0')]
    private string $id;

    #[OA\Property(ref: new Model(type: WasteMaterialSummaryDTO::class))]
    private WasteMaterialSummaryDTO $wasteMaterial;

    #[OA\Property(example: 120.5)]
    private float $volume;

    #[OA\Property(example: false)]
    private bool $isRecycled;

    public function __construct(
        string $id,
        WasteMaterialSummaryDTO $wasteMaterial,
        float $volume,
        bool $isRecycled,
    ) {
        $this->id = $id;
        $this->wasteMaterial = $wasteMaterial;
        $this->volume = $volume;
        $this->isRecycled = $isRecycled;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getWasteMaterial(): WasteMaterialSummaryDTO
    {
        return $this->wasteMaterial;
    }

    public function getVolume(): float
    {
        return $this->volume;
    }

    public function getIsRecycled(): bool
    {
        return $this->isRecycled;
    }
}
