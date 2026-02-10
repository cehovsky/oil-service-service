<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class EngineFilterCreateResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: EngineFilterDTO::class))]
    private EngineFilterDTO $engineFilter;

    public function __construct(string $result, int $timestamp, EngineFilterDTO $engineFilter)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->engineFilter = $engineFilter;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getEngineFilter(): EngineFilterDTO
    {
        return $this->engineFilter;
    }
}
