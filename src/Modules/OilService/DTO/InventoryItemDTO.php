<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class InventoryItemDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'Cabin filter')]
    private string $label;

    #[OA\Property(example: 'Replacement cabin filter', nullable: true)]
    private ?string $description;

    #[OA\Property(example: '1200.00', nullable: true)]
    private ?string $price;

    #[OA\Property(example: 21, nullable: true)]
    private ?int $vat;

    #[OA\Property(example: '1452.00', nullable: true)]
    private ?string $priceVat;

    #[OA\Property(example: 10)]
    private int $stockCount;

    #[OA\Property(example: '2026-01-05T12:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-01-05T12:00:00+00:00')]
    private string $updatedAt;

    #[OA\Property(example: '5d812c3d-31c4-4d13-a7c5-98b35ab63c9f')]
    private string $createdByUserId;

    #[OA\Property(example: '5d812c3d-31c4-4d13-a7c5-98b35ab63c9f')]
    private string $updatedByUserId;

    /**
     * @var InventoryItemHistoryDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: InventoryItemHistoryDTO::class)))]
    private array $history;

    /**
     * @param InventoryItemHistoryDTO[] $history
     */
    public function __construct(
        string $id,
        string $label,
        ?string $description,
        ?string $price,
        ?int $vat,
        ?string $priceVat,
        int $stockCount,
        string $createdAt,
        string $updatedAt,
        string $createdByUserId,
        string $updatedByUserId,
        array $history,
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->description = $description;
        $this->price = $price;
        $this->vat = $vat;
        $this->priceVat = $priceVat;
        $this->stockCount = $stockCount;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->createdByUserId = $createdByUserId;
        $this->updatedByUserId = $updatedByUserId;
        $this->history = $history;
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

    public function getStockCount(): int
    {
        return $this->stockCount;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getCreatedByUserId(): string
    {
        return $this->createdByUserId;
    }

    public function getUpdatedByUserId(): string
    {
        return $this->updatedByUserId;
    }

    /**
     * @return InventoryItemHistoryDTO[]
     */
    public function getHistory(): array
    {
        return $this->history;
    }
}
