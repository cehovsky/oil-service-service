<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class StorageContainerMaterialInfoResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: StorageContainerMaterialDTO::class))]
    private StorageContainerMaterialDTO $storageContainerMaterial;

    public function __construct(string $result, int $timestamp, StorageContainerMaterialDTO $storageContainerMaterial)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->storageContainerMaterial = $storageContainerMaterial;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getStorageContainerMaterial(): StorageContainerMaterialDTO
    {
        return $this->storageContainerMaterial;
    }
}
