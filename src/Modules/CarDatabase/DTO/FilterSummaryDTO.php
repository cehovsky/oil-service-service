<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use OpenApi\Attributes as OA;

class FilterSummaryDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'oil')]
    private string $filterType;

    #[OA\Property(example: 'mann')]
    private string $manufacturer;

    #[OA\Property(example: 'W 712/95')]
    private string $code;

    #[OA\Property(example: '03L115562', nullable: true)]
    private ?string $oemCode;

    public function __construct(
        string $id,
        string $filterType,
        string $manufacturer,
        string $code,
        ?string $oemCode,
    ) {
        $this->id = $id;
        $this->filterType = $filterType;
        $this->manufacturer = $manufacturer;
        $this->code = $code;
        $this->oemCode = $oemCode;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFilterType(): string
    {
        return $this->filterType;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getOemCode(): ?string
    {
        return $this->oemCode;
    }
}
