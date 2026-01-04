<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;

class StorageContainerSummaryDTO
{
    #[OA\Property(example: 'c2a63a4d-46b4-4d3b-98e3-d7343776e0b1')]
    private string $id;

    #[OA\Property(example: 'SC-001')]
    private string $code;

    #[OA\Property(example: 'barrel')]
    private string $type;

    #[OA\Property(example: 'l')]
    private string $volumeUnit;

    public function __construct(string $id, string $code, string $type, string $volumeUnit)
    {
        $this->id = $id;
        $this->code = $code;
        $this->type = $type;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function getVolumeUnit(): string
    {
        return $this->volumeUnit;
    }
}
