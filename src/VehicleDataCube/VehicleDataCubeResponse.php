<?php

declare(strict_types=1);

namespace App\VehicleDataCube;

class VehicleDataCubeResponse
{
    /**
     * @param array<string, mixed>|null $data
     */
    public function __construct(
        private readonly int $status,
        private readonly ?array $data,
        private readonly ?string $errorMessage = null,
    ) {
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
