<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Modules\Warehouse\Validation\Constraint\UniqueWasteMaterialCode;
use App\Warehouse\DBAL\Enum\VolumeUnitEnum;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueWasteMaterialCode]
class WasteMaterialCreateRequestDTO
{
    #[OA\Property(example: 'WM-01')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $code;

    #[OA\Property(example: 'Used oil')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $label;

    #[OA\Property(example: 'Oil')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $shortLabel;

    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isActive;

    #[OA\Property(example: 'l')]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [VolumeUnitEnum::class, 'values'])]
    private string $volumeUnit;

    #[OA\Property(example: 'Description from catalog')]
    #[Assert\Length(max: 255)]
    private ?string $catalogDescription = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getShortLabel(): string
    {
        return $this->shortLabel;
    }

    public function setShortLabel(string $shortLabel): self
    {
        $this->shortLabel = $shortLabel;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getVolumeUnit(): string
    {
        return $this->volumeUnit;
    }

    public function setVolumeUnit(string $volumeUnit): self
    {
        $this->volumeUnit = $volumeUnit;

        return $this;
    }

    public function getCatalogDescription(): ?string
    {
        return $this->catalogDescription;
    }

    public function setCatalogDescription(?string $catalogDescription): self
    {
        $this->catalogDescription = $catalogDescription;

        return $this;
    }
}
