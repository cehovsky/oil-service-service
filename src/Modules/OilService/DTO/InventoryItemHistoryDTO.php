<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\OilService\DBAL\Enum\InventoryMovementTypeEnum;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class InventoryItemHistoryDTO
{
    #[OA\Property(example: 'f9b9d3f2-5d1a-4b84-9d5c-8f3f31a5e5c2')]
    private string $id;

    #[OA\Property(enum: InventoryMovementTypeEnum::VALUES, example: 'stock_in')]
    private string $movementType;

    #[OA\Property(example: 5)]
    private int $quantity;

    #[OA\Property(example: true)]
    private bool $isIncrement;

    #[OA\Property(example: '1200.00', nullable: true)]
    private ?string $price;

    #[OA\Property(example: 21, nullable: true)]
    private ?int $vat;

    #[OA\Property(example: '1452.00', nullable: true)]
    private ?string $priceVat;

    #[OA\Property(example: 'Supplier delivery', nullable: true)]
    private ?string $note;

    #[OA\Property(ref: new Model(type: OrderSummaryDTO::class), nullable: true)]
    private ?OrderSummaryDTO $order;

    #[OA\Property(example: '2026-01-05T12:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '5d812c3d-31c4-4d13-a7c5-98b35ab63c9f')]
    private string $createdByUserId;

    public function __construct(
        string $id,
        string $movementType,
        int $quantity,
        bool $isIncrement,
        ?string $price,
        ?int $vat,
        ?string $priceVat,
        ?string $note,
        ?OrderSummaryDTO $order,
        string $createdAt,
        string $createdByUserId,
    ) {
        $this->id = $id;
        $this->movementType = $movementType;
        $this->quantity = $quantity;
        $this->isIncrement = $isIncrement;
        $this->price = $price;
        $this->vat = $vat;
        $this->priceVat = $priceVat;
        $this->note = $note;
        $this->order = $order;
        $this->createdAt = $createdAt;
        $this->createdByUserId = $createdByUserId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMovementType(): string
    {
        return $this->movementType;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getIsIncrement(): bool
    {
        return $this->isIncrement;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function getVat(): ?int
    {
        return $this->vat;
    }

    public function getPriceVat(): ?string
    {
        return $this->priceVat;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getOrder(): ?OrderSummaryDTO
    {
        return $this->order;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getCreatedByUserId(): string
    {
        return $this->createdByUserId;
    }
}
