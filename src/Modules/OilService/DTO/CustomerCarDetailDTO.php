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

    /** @var CustomerCarEngineFilterDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: CustomerCarEngineFilterDTO::class)))]
    private array $engineFilters;

    /**
     * @param CustomerCarHistoryDTO[] $history
     * @param CustomerCarEngineFilterDTO[] $engineFilters
     */
    public function __construct(CustomerCarDTO $car, array $history, array $engineFilters)
    {
        $this->car = $car;
        $this->history = $history;
        $this->engineFilters = $engineFilters;
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

    /**
     * @return CustomerCarEngineFilterDTO[]
     */
    public function getEngineFilters(): array
    {
        return $this->engineFilters;
    }
}
