<?php

declare(strict_types=1);

namespace App\Modules\Users\DTO;

use App\Domain\DTOValueResolver;
use OpenApi\Attributes as OA;

class UsersDeleteResponseDTO
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

    public function setResult(string $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
