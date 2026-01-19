<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class OrderInventoryItemDTO
{
    #[OA\Property(ref: new Model(type: InventoryItemSummaryDTO::class))]
    private InventoryItemSummaryDTO $inventoryItem;

    #[OA\Property(example: 3)]
    private int $quantity;

    public function __construct(InventoryItemSummaryDTO $inventoryItem, int $quantity)
    {
        $this->inventoryItem = $inventoryItem;
        $this->quantity = $quantity;
    }

    public function getInventoryItem(): InventoryItemSummaryDTO
    {
        return $this->inventoryItem;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
