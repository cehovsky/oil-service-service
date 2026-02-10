<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use App\CarDatabase\DBAL\Enum\FilterManufacturerEnum;
use App\CarDatabase\DBAL\Enum\FilterTypeEnum;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class FilterUpdateRequestDTO
{
    #[OA\Property(enum: FilterTypeEnum::VALUES, example: 'oil')]
    #[Assert\Choice(callback: [FilterTypeEnum::class, 'values'])]
    private string $filterType;

    #[OA\Property(enum: FilterManufacturerEnum::VALUES, example: 'mann')]
    #[Assert\Choice(callback: [FilterManufacturerEnum::class, 'values'])]
    #[Assert\Length(max: 255)]
    private string $manufacturer;

    #[OA\Property(example: 'W 712/95')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $code;

    #[OA\Property(example: '03L115562', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $oemCode = null;

    #[OA\Property(example: 'M20x1.5', nullable: true)]
    #[Assert\Length(max: 64)]
    private ?string $thread = null;

    #[OA\Property(example: 100, nullable: true)]
    #[Assert\Positive]
    private ?int $heightMm = null;

    #[OA\Property(example: 76, nullable: true)]
    #[Assert\Positive]
    private ?int $diameterMm = null;

    #[OA\Property(nullable: true)]
    private ?string $notes = null;

    public function getFilterType(): string
    {
        return $this->filterType;
    }

    public function setFilterType(string $filterType): self
    {
        $this->filterType = $filterType;

        return $this;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getOemCode(): ?string
    {
        return $this->oemCode;
    }

    public function setOemCode(?string $oemCode): self
    {
        $this->oemCode = $oemCode;

        return $this;
    }

    public function getThread(): ?string
    {
        return $this->thread;
    }

    public function setThread(?string $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    public function getHeightMm(): ?int
    {
        return $this->heightMm;
    }

    public function setHeightMm(?int $heightMm): self
    {
        $this->heightMm = $heightMm;

        return $this;
    }

    public function getDiameterMm(): ?int
    {
        return $this->diameterMm;
    }

    public function setDiameterMm(?int $diameterMm): self
    {
        $this->diameterMm = $diameterMm;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }
}
