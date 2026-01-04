<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class StorageContainerActualLocationDTO
{
    #[OA\Property(example: '2d9d82b9-7c1e-4fb4-9357-12a9e52b1f0e')]
    private string $locationId;

    #[OA\Property(example: '2026-01-04T00:00:00+00:00')]
    private string $movedAt;

    #[OA\Property(example: 'warehouse')]
    private string $locationType;

    #[OA\Property(ref: new Model(type: WarehouseSummaryDTO::class), nullable: true)]
    private ?WarehouseSummaryDTO $warehouse;

    #[OA\Property(ref: new Model(type: RouteSummaryDTO::class), nullable: true)]
    private ?RouteSummaryDTO $route;

    public function __construct(
        string $locationId,
        string $movedAt,
        string $locationType,
        ?WarehouseSummaryDTO $warehouse,
        ?RouteSummaryDTO $route,
    ) {
        $this->locationId = $locationId;
        $this->movedAt = $movedAt;
        $this->locationType = $locationType;
        $this->warehouse = $warehouse;
        $this->route = $route;
    }

    public function getLocationId(): string
    {
        return $this->locationId;
    }

    public function getMovedAt(): string
    {
        return $this->movedAt;
    }

    public function getLocationType(): string
    {
        return $this->locationType;
    }

    public function getWarehouse(): ?WarehouseSummaryDTO
    {
        return $this->warehouse;
    }

    public function getRoute(): ?RouteSummaryDTO
    {
        return $this->route;
    }
}
