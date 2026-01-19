<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class InventoryItemSummaryDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'Cabin filter')]
    private string $label;

    #[OA\Property(example: 12)]
    private int $stockCount;

    public function __construct(string $id, string $label, int $stockCount)
    {
        $this->id = $id;
        $this->label = $label;
        $this->stockCount = $stockCount;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getStockCount(): int
    {
        return $this->stockCount;
    }
}
