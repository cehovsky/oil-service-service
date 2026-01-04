<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;

class WasteMaterialDTO
{
    #[OA\Property(example: '1f391a20-4412-4bb3-99c6-873f1e0c1234')]
    private string $id;

    #[OA\Property(example: 'WM-01')]
    private string $code;

    #[OA\Property(example: 'Used oil')]
    private string $label;

    #[OA\Property(example: 'Oil')]
    private string $shortLabel;

    #[OA\Property(example: true)]
    private bool $isActive;

    #[OA\Property(example: 'l')]
    private string $volumeUnit;

    #[OA\Property(example: '2026-01-02T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-01-03T12:00:00+00:00')]
    private string $updatedAt;

    public function __construct(
        string $id,
        string $code,
        string $label,
        string $shortLabel,
        bool $isActive,
        string $volumeUnit,
        string $createdAt,
        string $updatedAt,
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->label = $label;
        $this->shortLabel = $shortLabel;
        $this->isActive = $isActive;
        $this->volumeUnit = $volumeUnit;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getShortLabel(): string
    {
        return $this->shortLabel;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getVolumeUnit(): string
    {
        return $this->volumeUnit;
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
