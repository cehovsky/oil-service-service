<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Modules\OilService\Validation\Constraint\ExistingOrderIds;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class RouteOrdersUpdateRequestDTO
{
    /**
     * @var string[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string', example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7'))]
    #[Assert\NotNull]
    #[Assert\All([
        new Assert\Uuid(),
    ])]
    #[ExistingOrderIds]
    private array $orderIds = [];

    /**
     * @return string[]
     */
    public function getOrderIds(): array
    {
        return $this->orderIds;
    }

    /**
     * @param string[] $orderIds
     */
    public function setOrderIds(array $orderIds): self
    {
        $this->orderIds = $orderIds;

        return $this;
    }
}
