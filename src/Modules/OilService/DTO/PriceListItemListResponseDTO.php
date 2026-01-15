<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class PriceListItemListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var PriceListItemDTO[]
     */
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            ref: new Model(
                type: PriceListItemDTO::class
            )
        )
    )]
    private array $priceListItems;

    private int $pageCount;

    /**
     * @param PriceListItemDTO[] $priceListItems
     */
    public function __construct(
        string $result,
        int $timestamp,
        array $priceListItems,
        int $pageCount,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->priceListItems = $priceListItems;
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
     * @return PriceListItemDTO[]
     */
    public function getPriceListItems(): array
    {
        return $this->priceListItems;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
