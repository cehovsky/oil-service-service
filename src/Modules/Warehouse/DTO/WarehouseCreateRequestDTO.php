<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class WarehouseCreateRequestDTO
{
    #[OA\Property(example: 'Central warehouse')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $label;

    #[OA\Property(example: 'CW-01')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $shortLabel;

    #[OA\Property(example: '123 Main St, Prague', nullable: true)]
    #[Assert\Length(max: 65535)]
    private ?string $address = null;

    #[OA\Property(example: 50.087, nullable: true)]
    #[Assert\Range(min: -90, max: 90)]
    private ?float $latitude = null;

    #[OA\Property(example: 14.421, nullable: true)]
    #[Assert\Range(min: -180, max: 180)]
    private ?float $longitude = null;

    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isActive;

    #[OA\Property(example: false)]
    #[Assert\NotNull]
    private bool $isGarage;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

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

    public function getIsGarage(): bool
    {
        return $this->isGarage;
    }

    public function setIsGarage(bool $isGarage): self
    {
        $this->isGarage = $isGarage;

        return $this;
    }
}
