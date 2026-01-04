<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;

class RouteSummaryDTO
{
    #[OA\Property(example: 'c8f430b6-6ddf-4ec1-8d16-1b65b7ebc432')]
    private string $id;

    #[OA\Property(example: '2026-01-05')]
    private string $date;

    #[OA\Property(example: true)]
    private bool $isActive;

    public function __construct(string $id, string $date, bool $isActive)
    {
        $this->id = $id;
        $this->date = $date;
        $this->isActive = $isActive;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }
}
