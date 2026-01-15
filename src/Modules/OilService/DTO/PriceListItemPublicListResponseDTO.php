<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class PriceListItemPublicListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var PriceListItemPublicDTO[]
     */
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            ref: new Model(
                type: PriceListItemPublicDTO::class
            )
        )
    )]
    private array $priceListItems;

    /**
     * @param PriceListItemPublicDTO[] $priceListItems
     */
    public function __construct(
        string $result,
        int $timestamp,
        array $priceListItems,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->priceListItems = $priceListItems;
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
     * @return PriceListItemPublicDTO[]
     */
    public function getPriceListItems(): array
    {
        return $this->priceListItems;
    }
}
