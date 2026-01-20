<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Modules\Users\DTO\UserDTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class StorageContainerMaterialHistoryDTO
{
    #[OA\Property(example: 'f9b9d3f2-5d1a-4b84-9d5c-8f3f31a5e5c2')]
    private string $id;

    #[OA\Property(ref: new Model(type: StorageContainerMaterialSummaryDTO::class))]
    private StorageContainerMaterialSummaryDTO $storageContainerMaterial;

    #[OA\Property(ref: new Model(type: StorageContainerSummaryDTO::class))]
    private StorageContainerSummaryDTO $storageContainer;

    #[OA\Property(example: '2026-01-05T12:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(ref: new Model(type: UserDTO::class))]
    private UserDTO $createdByUser;

    public function __construct(
        string $id,
        StorageContainerMaterialSummaryDTO $storageContainerMaterial,
        StorageContainerSummaryDTO $storageContainer,
        string $createdAt,
        UserDTO $createdByUser,
    ) {
        $this->id = $id;
        $this->storageContainerMaterial = $storageContainerMaterial;
        $this->storageContainer = $storageContainer;
        $this->createdAt = $createdAt;
        $this->createdByUser = $createdByUser;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStorageContainerMaterial(): StorageContainerMaterialSummaryDTO
    {
        return $this->storageContainerMaterial;
    }

    public function getStorageContainer(): StorageContainerSummaryDTO
    {
        return $this->storageContainer;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getCreatedByUser(): UserDTO
    {
        return $this->createdByUser;
    }
}
