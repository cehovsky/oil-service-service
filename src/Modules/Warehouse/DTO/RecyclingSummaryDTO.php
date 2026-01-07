<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;

class RecyclingSummaryDTO
{
    #[OA\Property(example: 'c4f0a6a6-6f80-4a0c-a4c2-23b7a6f21f9d')]
    private string $id;

    #[OA\Property(example: '2026-01-06')]
    private ?string $recycledAt;

    #[OA\Property(example: '5d812c3d-31c4-4d13-a7c5-98b35ab63c9f', nullable: true)]
    private ?string $recycledByUserId;

    public function __construct(string $id, ?string $recycledAt, ?string $recycledByUserId)
    {
        $this->id = $id;
        $this->recycledAt = $recycledAt;
        $this->recycledByUserId = $recycledByUserId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRecycledAt(): ?string
    {
        return $this->recycledAt;
    }

    public function getRecycledByUserId(): ?string
    {
        return $this->recycledByUserId;
    }
}
