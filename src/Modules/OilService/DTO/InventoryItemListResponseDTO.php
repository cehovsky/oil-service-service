<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class InventoryItemListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var InventoryItemDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: InventoryItemDTO::class)))]
    private array $inventoryItems;

    private int $pageCount;

    /**
     * @param InventoryItemDTO[] $inventoryItems
     */
    public function __construct(
        string $result,
        int $timestamp,
        array $inventoryItems,
        int $pageCount,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->inventoryItems = $inventoryItems;
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
     * @return InventoryItemDTO[]
     */
    public function getInventoryItems(): array
    {
        return $this->inventoryItems;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
