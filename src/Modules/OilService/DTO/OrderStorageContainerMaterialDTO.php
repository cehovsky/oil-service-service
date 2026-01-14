<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Modules\Warehouse\DTO\RecyclingSummaryDTO;
use App\Modules\Warehouse\DTO\RouteSummaryDTO;
use App\Modules\Warehouse\DTO\StorageContainerSummaryDTO;
use App\Modules\Warehouse\DTO\WarehouseSummaryDTO;
use App\Modules\Warehouse\DTO\WasteMaterialSummaryDTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class OrderStorageContainerMaterialDTO
{
    #[OA\Property(example: '2aab3a6d-1bde-47b2-8f12-a6120f8470c0')]
    private string $id;

    #[OA\Property(ref: new Model(type: StorageContainerSummaryDTO::class))]
    private StorageContainerSummaryDTO $storageContainer;

    #[OA\Property(ref: new Model(type: WasteMaterialSummaryDTO::class))]
    private WasteMaterialSummaryDTO $wasteMaterial;

    #[OA\Property(ref: new Model(type: WarehouseSummaryDTO::class), nullable: true)]
    private ?WarehouseSummaryDTO $warehouse;

    #[OA\Property(ref: new Model(type: RouteSummaryDTO::class), nullable: true)]
    private ?RouteSummaryDTO $route;

    #[OA\Property(ref: new Model(type: RecyclingSummaryDTO::class), nullable: true)]
    private ?RecyclingSummaryDTO $recycling;

    #[OA\Property(example: 120.5)]
    private float $volume;

    #[OA\Property(example: false)]
    private bool $isRecycled;

    #[OA\Property(example: '2026-01-04T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-01-05T10:00:00+00:00')]
    private string $updatedAt;

    public function __construct(
        string $id,
        StorageContainerSummaryDTO $storageContainer,
        WasteMaterialSummaryDTO $wasteMaterial,
        ?WarehouseSummaryDTO $warehouse,
        ?RouteSummaryDTO $route,
        ?RecyclingSummaryDTO $recycling,
        float $volume,
        bool $isRecycled,
        string $createdAt,
        string $updatedAt,
    ) {
        $this->id = $id;
        $this->storageContainer = $storageContainer;
        $this->wasteMaterial = $wasteMaterial;
        $this->warehouse = $warehouse;
        $this->route = $route;
        $this->recycling = $recycling;
        $this->volume = $volume;
        $this->isRecycled = $isRecycled;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStorageContainer(): StorageContainerSummaryDTO
    {
        return $this->storageContainer;
    }

    public function getWasteMaterial(): WasteMaterialSummaryDTO
    {
        return $this->wasteMaterial;
    }

    public function getWarehouse(): ?WarehouseSummaryDTO
    {
        return $this->warehouse;
    }

    public function getRoute(): ?RouteSummaryDTO
    {
        return $this->route;
    }

    public function getRecycling(): ?RecyclingSummaryDTO
    {
        return $this->recycling;
    }

    public function getVolume(): float
    {
        return $this->volume;
    }

    public function getIsRecycled(): bool
    {
        return $this->isRecycled;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
