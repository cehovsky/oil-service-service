<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class InventoryItemStockInRequestDTO
{
    #[OA\Property(example: 5)]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $quantity;

    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7', nullable: true)]
    #[Assert\Uuid]
    private ?string $orderId = null;

    #[OA\Property(example: '1200.00', nullable: true)]
    #[Assert\Regex(pattern: '/^\d+(?:\.\d{1,2})?$/')]
    private ?string $price = null;

    #[OA\Property(example: 21, nullable: true)]
    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 100)]
    private ?int $vat = null;

    #[OA\Property(example: 'Supplier delivery', nullable: true)]
    #[Assert\Length(max: 2000)]
    private ?string $note = null;

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(?string $orderId): self
    {
        $this->orderId = $orderId;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
