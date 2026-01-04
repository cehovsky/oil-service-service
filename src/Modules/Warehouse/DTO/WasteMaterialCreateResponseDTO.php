<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class WasteMaterialCreateResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735980000)]
    private int $timestamp;

    #[OA\Property(ref: new Model(type: WasteMaterialDTO::class))]
    private WasteMaterialDTO $wasteMaterial;

    public function __construct(string $result, int $timestamp, WasteMaterialDTO $wasteMaterial)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->wasteMaterial = $wasteMaterial;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getWasteMaterial(): WasteMaterialDTO
    {
        return $this->wasteMaterial;
    }
}
