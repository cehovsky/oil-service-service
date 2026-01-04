<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;

class WarehouseSummaryDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'Central warehouse')]
    private string $label;

    #[OA\Property(example: 'CW-01')]
    private string $shortLabel;

    public function __construct(string $id, string $label, string $shortLabel)
    {
        $this->id = $id;
        $this->label = $label;
        $this->shortLabel = $shortLabel;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getShortLabel(): string
    {
        return $this->shortLabel;
    }
}
