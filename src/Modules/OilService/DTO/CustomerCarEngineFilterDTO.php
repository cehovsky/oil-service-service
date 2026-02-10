<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Modules\CarDatabase\DTO\FilterSummaryDTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class CustomerCarEngineFilterDTO
{
    #[OA\Property(ref: new Model(type: FilterSummaryDTO::class))]
    private FilterSummaryDTO $filter;

    #[OA\Property(ref: new Model(type: InventoryItemSummaryDTO::class), nullable: true)]
    private ?InventoryItemSummaryDTO $inventoryItem;

    #[OA\Property(example: true)]
    private bool $isPrimary;

    #[OA\Property(example: 'MANN', nullable: true)]
    private ?string $source;

    public function __construct(
        FilterSummaryDTO $filter,
        ?InventoryItemSummaryDTO $inventoryItem,
        bool $isPrimary,
        ?string $source,
    ) {
        $this->filter = $filter;
        $this->inventoryItem = $inventoryItem;
        $this->isPrimary = $isPrimary;
        $this->source = $source;
    }

    public function getFilter(): FilterSummaryDTO
    {
        return $this->filter;
    }

    public function getInventoryItem(): ?InventoryItemSummaryDTO
    {
        return $this->inventoryItem;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }
}
