<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use OpenApi\Attributes as OA;

class EngineSummaryDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'skoda')]
    private string $manufacturer;

    #[OA\Property(example: 'Octavia')]
    private string $model;

    #[OA\Property(example: 'CHYA', nullable: true)]
    private ?string $engineCode;

    public function __construct(
        string $id,
        string $manufacturer,
        string $model,
        ?string $engineCode,
    ) {
        $this->id = $id;
        $this->manufacturer = $manufacturer;
        $this->model = $model;
        $this->engineCode = $engineCode;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getEngineCode(): ?string
    {
        return $this->engineCode;
    }
}
