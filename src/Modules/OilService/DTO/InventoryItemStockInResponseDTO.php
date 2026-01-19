<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class InventoryItemStockInResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: InventoryItemDTO::class))]
    private InventoryItemDTO $inventoryItem;

    public function __construct(
        string $result,
        int $timestamp,
        InventoryItemDTO $inventoryItem,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->inventoryItem = $inventoryItem;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getInventoryItem(): InventoryItemDTO
    {
        return $this->inventoryItem;
    }
}
