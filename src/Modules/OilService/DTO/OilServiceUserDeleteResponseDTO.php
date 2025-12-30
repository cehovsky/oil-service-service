<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use OpenApi\Attributes as OA;

class OilServiceUserDeleteResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    public function __construct(
        string $result,
        int $timestamp
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }
}
