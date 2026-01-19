<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class OrderInventoryItemUpdateItemDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private string $inventoryItemId;

    #[OA\Property(example: 2)]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $quantity;

    public function getInventoryItemId(): string
    {
        return $this->inventoryItemId;
    }

    public function setInventoryItemId(string $inventoryItemId): self
    {
        $this->inventoryItemId = $inventoryItemId;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
