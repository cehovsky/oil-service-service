<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class WarehouseListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var WarehouseDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: WarehouseDTO::class)))]
    private array $warehouses;

    private int $pageCount;

    /**
     * @param WarehouseDTO[] $warehouses
     */
    public function __construct(string $result, int $timestamp, array $warehouses, int $pageCount)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->warehouses = $warehouses;
        $this->pageCount = $pageCount;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return WarehouseDTO[]
     */
    public function getWarehouses(): array
    {
        return $this->warehouses;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
