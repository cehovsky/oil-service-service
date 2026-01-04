<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class WarehouseDetailDTO
{
    #[OA\Property(ref: new Model(type: WarehouseDTO::class))]
    private WarehouseDTO $warehouse;

    /**
     * @var WarehouseCurrentLocationDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: WarehouseCurrentLocationDTO::class)))]
    private array $currentLocations;

    /**
     * @param WarehouseCurrentLocationDTO[] $currentLocations
     */
    public function __construct(WarehouseDTO $warehouse, array $currentLocations)
    {
        $this->warehouse = $warehouse;
        $this->currentLocations = $currentLocations;
    }

    public function getWarehouse(): WarehouseDTO
    {
        return $this->warehouse;
    }

    /**
     * @return WarehouseCurrentLocationDTO[]
     */
    public function getCurrentLocations(): array
    {
        return $this->currentLocations;
    }
}
