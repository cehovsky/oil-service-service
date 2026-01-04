<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;

class WarehouseDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'Central warehouse')]
    private string $label;

    #[OA\Property(example: 'CW-01')]
    private string $shortLabel;

    #[OA\Property(example: '123 Main St, Prague', nullable: true)]
    private ?string $address;

    #[OA\Property(example: true)]
    private bool $isActive;

    #[OA\Property(example: '2026-01-02T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-01-03T12:00:00+00:00')]
    private string $updatedAt;

    public function __construct(
        string $id,
        string $label,
        string $shortLabel,
        ?string $address,
        bool $isActive,
        string $createdAt,
        string $updatedAt,
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->shortLabel = $shortLabel;
        $this->address = $address;
        $this->isActive = $isActive;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getShortLabel(): string
    {
        return $this->shortLabel;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
