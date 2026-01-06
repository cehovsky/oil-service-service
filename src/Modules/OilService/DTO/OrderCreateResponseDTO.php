<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use OpenApi\Attributes as OA;

class OrderCreateResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    private bool $success;

    public function __construct(
        string $result,
        int $timestamp,
        bool $success,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->success = $success;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }
}
