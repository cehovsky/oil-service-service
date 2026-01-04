<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class WarehouseCreateResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735980000)]
    private int $timestamp;

    #[OA\Property(ref: new Model(type: WarehouseDTO::class))]
    private WarehouseDTO $warehouse;

    public function __construct(string $result, int $timestamp, WarehouseDTO $warehouse)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->warehouse = $warehouse;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getWarehouse(): WarehouseDTO
    {
        return $this->warehouse;
    }
}
