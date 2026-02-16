<?php

declare(strict_types=1);

namespace App\Modules\CarApp\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class OrderMaterialsUpdateRequestDTO
{
    /**
     * @var OrderMaterialsUpdateItemDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: OrderMaterialsUpdateItemDTO::class)))]
    #[Assert\NotNull]
    #[Assert\All([
        new Assert\Type(OrderMaterialsUpdateItemDTO::class),
    ])]
    #[Assert\Valid]
    private array $items = [];

    /**
     * @return OrderMaterialsUpdateItemDTO[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param OrderMaterialsUpdateItemDTO[] $items
     */
    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }
}
