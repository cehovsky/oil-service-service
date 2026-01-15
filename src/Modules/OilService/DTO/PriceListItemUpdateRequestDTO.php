<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class PriceListItemUpdateRequestDTO
{
    #[OA\Property(example: 'Oil change')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $label;

    #[OA\Property(example: 'Standard oil change', nullable: true)]
    #[Assert\Length(max: 2000)]
    private ?string $description = null;

    #[OA\Property(example: 'Oil change service', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $invoiceLabel = null;

    #[OA\Property(example: '1200.00')]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^\d+(?:\.\d{1,2})?$/')]
    private string $price;

    #[OA\Property(example: 21)]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 100)]
    private int $vat;

    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isActive;

    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isPublic;

    #[OA\Property(example: false)]
    #[Assert\NotNull]
    private bool $isDefault;

    #[OA\Property(example: false)]
    #[Assert\NotNull]
    private bool $isHiddenOnInvoice;

    #[OA\Property(example: '1001')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Assert\Regex(pattern: '/^\d+$/')]
    private string $code;

    #[OA\Property(example: 'Shell', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $brand = null;

    #[OA\Property(example: 'EXT-001', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $externalCode = null;

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

    public function getInvoiceLabel(): ?string
    {
        return $this->invoiceLabel;
    }

    public function setInvoiceLabel(?string $invoiceLabel): self
    {
        $this->invoiceLabel = $invoiceLabel;

        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getVat(): int
    {
        return $this->vat;
    }

    public function setVat(int $vat): self
    {
        $this->vat = $vat;

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

    public function getIsPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function getIsHiddenOnInvoice(): bool
    {
        return $this->isHiddenOnInvoice;
    }

    public function setIsHiddenOnInvoice(bool $isHiddenOnInvoice): self
    {
        $this->isHiddenOnInvoice = $isHiddenOnInvoice;

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

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getExternalCode(): ?string
    {
        return $this->externalCode;
    }

    public function setExternalCode(?string $externalCode): self
    {
        $this->externalCode = $externalCode;

        return $this;
    }
}
