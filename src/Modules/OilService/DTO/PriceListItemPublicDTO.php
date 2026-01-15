<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class PriceListItemPublicDTO
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

    public function __construct(
        string $id,
        string $label,
        ?string $description,
        ?string $invoiceLabel,
        string $price,
        int $vat,
        string $priceVat,
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->description = $description;
        $this->invoiceLabel = $invoiceLabel;
        $this->price = $price;
        $this->vat = $vat;
        $this->priceVat = $priceVat;
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
}
