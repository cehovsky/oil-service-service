<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class StorageContainerLocationDTO
{
    #[OA\Property(example: '2d9d82b9-7c1e-4fb4-9357-12a9e52b1f0e')]
    private string $id;

    #[OA\Property(ref: new Model(type: StorageContainerSummaryDTO::class))]
    private StorageContainerSummaryDTO $storageContainer;

    #[OA\Property(ref: new Model(type: WarehouseSummaryDTO::class), nullable: true)]
    private ?WarehouseSummaryDTO $warehouse;

    #[OA\Property(ref: new Model(type: RouteSummaryDTO::class), nullable: true)]
    private ?RouteSummaryDTO $route;

    #[OA\Property(example: '2026-01-04T00:00:00+00:00')]
    private string $movedAt;

    #[OA\Property(example: '2026-01-03T12:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-01-03T12:00:00+00:00')]
    private string $updatedAt;

    public function __construct(
        string $id,
        StorageContainerSummaryDTO $storageContainer,
        ?WarehouseSummaryDTO $warehouse,
        ?RouteSummaryDTO $route,
        string $movedAt,
        string $createdAt,
        string $updatedAt,
    ) {
        $this->id = $id;
        $this->storageContainer = $storageContainer;
        $this->warehouse = $warehouse;
        $this->route = $route;
        $this->movedAt = $movedAt;
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

    public function getWarehouse(): ?WarehouseSummaryDTO
    {
        return $this->warehouse;
    }

    public function getRoute(): ?RouteSummaryDTO
    {
        return $this->route;
    }

    public function getMovedAt(): string
    {
        return $this->movedAt;
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
