<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class PriceListItemDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'Oil change')]
    private string $label;

    #[OA\Property(example: 'Standard oil change', nullable: true)]
    private ?string $description;

    #[OA\Property(example: 'Oil change service', nullable: true)]
    private ?string $invoiceLabel;

    #[OA\Property(example: '1200.00')]
    private string $price;

    #[OA\Property(example: 21)]
    private int $vat;

    #[OA\Property(example: '1452.00')]
    private string $priceVat;

    #[OA\Property(example: true)]
    private bool $isActive;

    #[OA\Property(example: true)]
    private bool $isPublic;

    #[OA\Property(example: false)]
    private bool $isDefault;

    #[OA\Property(example: false)]
    private bool $isHiddenOnInvoice;

    #[OA\Property(example: '1001')]
    private string $code;

    #[OA\Property(example: 'Shell', nullable: true)]
    private ?string $brand;

    #[OA\Property(example: 'EXT-001', nullable: true)]
    private ?string $externalCode;

    #[OA\Property(example: '2026-01-14T10:00:00+00:00')]
    private string $createdAt;

    public function __construct(
        string $id,
        string $label,
        ?string $description,
        ?string $invoiceLabel,
        string $price,
        int $vat,
        string $priceVat,
        bool $isActive,
        bool $isPublic,
        bool $isDefault,
        bool $isHiddenOnInvoice,
        string $code,
        ?string $brand,
        ?string $externalCode,
        string $createdAt,
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->description = $description;
        $this->invoiceLabel = $invoiceLabel;
        $this->price = $price;
        $this->vat = $vat;
        $this->priceVat = $priceVat;
        $this->isActive = $isActive;
        $this->isPublic = $isPublic;
        $this->isDefault = $isDefault;
        $this->isHiddenOnInvoice = $isHiddenOnInvoice;
        $this->code = $code;
        $this->brand = $brand;
        $this->externalCode = $externalCode;
        $this->createdAt = $createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getInvoiceLabel(): ?string
    {
        return $this->invoiceLabel;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function getVat(): int
    {
        return $this->vat;
    }

    public function getPriceVat(): string
    {
        return $this->priceVat;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getIsPublic(): bool
    {
        return $this->isPublic;
    }

    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function getIsHiddenOnInvoice(): bool
    {
        return $this->isHiddenOnInvoice;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function getExternalCode(): ?string
    {
        return $this->externalCode;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
