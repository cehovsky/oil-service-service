<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class PriceListItemCreateResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: PriceListItemDTO::class))]
    private PriceListItemDTO $priceListItem;

    public function __construct(
        string $result,
        int $timestamp,
        PriceListItemDTO $priceListItem,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->priceListItem = $priceListItem;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getPriceListItem(): PriceListItemDTO
    {
        return $this->priceListItem;
    }
}
