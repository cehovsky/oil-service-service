<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class CustomerCarDetailDTO
{
    #[OA\Property(ref: new Model(type: CustomerCarDTO::class))]
    private CustomerCarDTO $car;

    /** @var CustomerCarHistoryDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: CustomerCarHistoryDTO::class)))]
    private array $history;

    /**
     * @param CustomerCarHistoryDTO[] $history
     */
    public function __construct(CustomerCarDTO $car, array $history)
    {
        $this->car = $car;
        $this->history = $history;
    }

    public function getCar(): CustomerCarDTO
    {
        return $this->car;
    }

    /**
     * @return CustomerCarHistoryDTO[]
     */
    public function getHistory(): array
    {
        return $this->history;
    }
}
