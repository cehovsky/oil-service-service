<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class OrderInventoryItemsUpdateRequestDTO
{
    /**
     * @var OrderInventoryItemUpdateItemDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: OrderInventoryItemUpdateItemDTO::class)))]
    #[Assert\NotNull]
    #[Assert\All([
        new Assert\Type(OrderInventoryItemUpdateItemDTO::class),
    ])]
    #[Assert\Valid]
    private array $items = [];

    /**
     * @return OrderInventoryItemUpdateItemDTO[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param OrderInventoryItemUpdateItemDTO[] $items
     */
    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }
}
