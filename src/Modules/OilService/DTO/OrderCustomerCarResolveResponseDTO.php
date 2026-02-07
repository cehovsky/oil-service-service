<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class OrderCustomerCarResolveResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: CustomerCarDTO::class))]
    private CustomerCarDTO $car;

    public function __construct(
        string $result,
        int $timestamp,
        CustomerCarDTO $car
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->car = $car;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getCar(): CustomerCarDTO
    {
        return $this->car;
    }
}
