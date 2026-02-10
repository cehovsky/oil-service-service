<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class EngineInfoResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: EngineDTO::class))]
    private EngineDTO $engine;

    public function __construct(string $result, int $timestamp, EngineDTO $engine)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->engine = $engine;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getEngine(): EngineDTO
    {
        return $this->engine;
    }
}
