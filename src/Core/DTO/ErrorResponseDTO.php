<?php

namespace App\Core\DTO;

use OpenApi\Attributes as OA;

class ErrorResponseDTO
{
    #[OA\Property(
        example: 'Server Error'
    )]
    private string $result;

    public function __construct(
        string $result
    ) {
        $this->result = $result;
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
}
