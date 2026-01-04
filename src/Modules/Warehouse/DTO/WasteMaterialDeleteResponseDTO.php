<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;

class WasteMaterialDeleteResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735980000)]
    private int $timestamp;

    public function __construct(string $result, int $timestamp)
    {
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
