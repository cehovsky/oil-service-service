<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class InventoryItemSummaryDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'Cabin filter')]
    private string $label;

    #[OA\Property(example: '1001')]
    private string $code;

    #[OA\Property(example: 'OEM-001', nullable: true)]
    private ?string $oemCode;

    #[OA\Property(example: 12)]
    private int $stockCount;

    public function __construct(string $id, string $label, string $code, ?string $oemCode, int $stockCount)
    {
        $this->id = $id;
        $this->label = $label;
        $this->code = $code;
        $this->oemCode = $oemCode;
        $this->stockCount = $stockCount;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getOemCode(): ?string
    {
        return $this->oemCode;
    }

    public function getStockCount(): int
    {
        return $this->stockCount;
    }
}
