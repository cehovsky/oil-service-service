<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class OrderCustomerCarConflictResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(example: false)]
    private bool $isConflict;

    #[OA\Property(ref: new Model(type: CustomerCarDTO::class), nullable: true)]
    private ?CustomerCarDTO $car;

    public function __construct(
        string $result,
        int $timestamp,
        bool $isConflict,
        ?CustomerCarDTO $car
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->isConflict = $isConflict;
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

    public function getIsConflict(): bool
    {
        return $this->isConflict;
    }

    public function getCar(): ?CustomerCarDTO
    {
        return $this->car;
    }
}
