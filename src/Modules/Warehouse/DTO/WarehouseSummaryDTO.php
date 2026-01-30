<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;

class WarehouseSummaryDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'Central warehouse')]
    private string $label;

    #[OA\Property(example: 'CW-01')]
    private string $shortLabel;

    #[OA\Property(example: 50.087, nullable: true)]
    private ?float $latitude;

    #[OA\Property(example: 14.421, nullable: true)]
    private ?float $longitude;

    #[OA\Property(example: false)]
    private bool $isGarage;

    public function __construct(
        string $id,
        string $label,
        string $shortLabel,
        ?float $latitude,
        ?float $longitude,
        bool $isGarage,
    )
    {
        $this->id = $id;
        $this->label = $label;
        $this->shortLabel = $shortLabel;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->isGarage = $isGarage;
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

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getIsGarage(): bool
    {
        return $this->isGarage;
    }
}
