<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;

class StorageContainerLocationBasicDTO
{
    #[OA\Property(example: '2d9d82b9-7c1e-4fb4-9357-12a9e52b1f0e')]
    private string $id;

    #[OA\Property(example: '2026-01-04T00:00:00+00:00')]
    private string $movedAt;

    public function __construct(string $id, string $movedAt)
    {
        $this->id = $id;
        $this->movedAt = $movedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMovedAt(): string
    {
        return $this->movedAt;
    }
}
