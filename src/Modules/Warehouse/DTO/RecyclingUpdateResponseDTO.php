<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class RecyclingUpdateResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: RecyclingDTO::class))]
    private RecyclingDTO $recycling;

    public function __construct(string $result, int $timestamp, RecyclingDTO $recycling)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->recycling = $recycling;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getRecycling(): RecyclingDTO
    {
        return $this->recycling;
    }
}
