<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class StorageContainerMaterialMoveResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var StorageContainerMaterialDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: StorageContainerMaterialDTO::class)))]
    private array $storageContainerMaterials;

    /**
     * @param StorageContainerMaterialDTO[] $storageContainerMaterials
     */
    public function __construct(string $result, int $timestamp, array $storageContainerMaterials)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->storageContainerMaterials = $storageContainerMaterials;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return StorageContainerMaterialDTO[]
     */
    public function getStorageContainerMaterials(): array
    {
        return $this->storageContainerMaterials;
    }
}
