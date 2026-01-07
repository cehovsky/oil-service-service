<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class StorageContainerMaterialHistoryListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var StorageContainerMaterialHistoryDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: StorageContainerMaterialHistoryDTO::class)))]
    private array $items;

    private int $pageCount;

    /**
     * @param StorageContainerMaterialHistoryDTO[] $items
     */
    public function __construct(string $result, int $timestamp, array $items, int $pageCount)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->items = $items;
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
     * @return StorageContainerMaterialHistoryDTO[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
