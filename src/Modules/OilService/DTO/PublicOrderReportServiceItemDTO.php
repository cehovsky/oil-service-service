<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class PublicOrderReportServiceItemDTO
{
    #[OA\Property(example: 'service')]
    private string $type;

    #[OA\Property(example: 'Výměna motorového oleje')]
    private string $label;

    #[OA\Property(example: 1, nullable: true)]
    private ?int $quantity;

    public function __construct(string $type, string $label, ?int $quantity = null)
    {
        $this->type = $type;
        $this->label = $label;
        $this->quantity = $quantity;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }
}
