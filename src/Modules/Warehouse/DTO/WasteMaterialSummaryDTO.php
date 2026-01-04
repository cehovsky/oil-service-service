<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;

class WasteMaterialSummaryDTO
{
    #[OA\Property(example: '1f391a20-4412-4bb3-99c6-873f1e0c1234')]
    private string $id;

    #[OA\Property(example: 'WM-01')]
    private string $code;

    #[OA\Property(example: 'Used oil')]
    private string $label;

    #[OA\Property(example: 'l')]
    private string $volumeUnit;

    public function __construct(string $id, string $code, string $label, string $volumeUnit)
    {
        $this->id = $id;
        $this->code = $code;
        $this->label = $label;
        $this->volumeUnit = $volumeUnit;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getVolumeUnit(): string
    {
        return $this->volumeUnit;
    }
}
