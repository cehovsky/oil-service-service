<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use OpenApi\Attributes as OA;

class FilterDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'oil')]
    private string $filterType;

    #[OA\Property(example: 'mann')]
    private string $manufacturer;

    #[OA\Property(example: 'W 712/95')]
    private string $code;

    #[OA\Property(example: '03L115562', nullable: true)]
    private ?string $oemCode;

    #[OA\Property(example: 'M20x1.5', nullable: true)]
    private ?string $thread;

    #[OA\Property(example: 100, nullable: true)]
    private ?int $heightMm;

    #[OA\Property(example: 76, nullable: true)]
    private ?int $diameterMm;

    #[OA\Property(nullable: true)]
    private ?string $notes;

    #[OA\Property(example: '2026-02-01T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-02-02T10:00:00+00:00')]
    private string $updatedAt;

    public function __construct(
        string $id,
        string $filterType,
        string $manufacturer,
        string $code,
        ?string $oemCode,
        ?string $thread,
        ?int $heightMm,
        ?int $diameterMm,
        ?string $notes,
        string $createdAt,
        string $updatedAt,
    ) {
        $this->id = $id;
        $this->filterType = $filterType;
        $this->manufacturer = $manufacturer;
        $this->code = $code;
        $this->oemCode = $oemCode;
        $this->thread = $thread;
        $this->heightMm = $heightMm;
        $this->diameterMm = $diameterMm;
        $this->notes = $notes;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFilterType(): string
    {
        return $this->filterType;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getOemCode(): ?string
    {
        return $this->oemCode;
    }

    public function getThread(): ?string
    {
        return $this->thread;
    }

    public function getHeightMm(): ?int
    {
        return $this->heightMm;
    }

    public function getDiameterMm(): ?int
    {
        return $this->diameterMm;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
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
