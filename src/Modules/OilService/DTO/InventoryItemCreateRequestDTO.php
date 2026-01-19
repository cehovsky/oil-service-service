<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class InventoryItemCreateRequestDTO
{
    #[OA\Property(example: 'Cabin filter')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $label;

    #[OA\Property(example: 'Replacement cabin filter', nullable: true)]
    #[Assert\Length(max: 2000)]
    private ?string $description = null;

    #[OA\Property(example: '1200.00', nullable: true)]
    #[Assert\Regex(pattern: '/^\d+(?:\.\d{1,2})?$/')]
    private ?string $price = null;

    #[OA\Property(example: 21, nullable: true)]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 100)]
    private ?int $vat = null;

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getVat(): ?int
    {
        return $this->vat;
    }

    public function setVat(?int $vat): self
    {
        $this->vat = $vat;

        return $this;
    }
}
