<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class CarUpdateResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: CarDTO::class))]
    private CarDTO $car;

    public function __construct(
        string $result,
        int $timestamp,
        CarDTO $car
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

    public function getCar(): CarDTO
    {
        return $this->car;
    }
}
