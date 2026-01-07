<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class RecyclingDTO
{
    #[OA\Property(example: 'c4f0a6a6-6f80-4a0c-a4c2-23b7a6f21f9d')]
    private string $id;

    #[OA\Property(example: '2026-01-06', nullable: true)]
    private ?string $recycledAt;

    #[OA\Property(example: '5d812c3d-31c4-4d13-a7c5-98b35ab63c9f', nullable: true)]
    private ?string $recycledByUserId;

    #[OA\Property(example: '2026-01-04T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-01-05T10:00:00+00:00')]
    private string $updatedAt;

    /**
     * @var StorageContainerSummaryDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: StorageContainerSummaryDTO::class)))]
    private array $storageContainers;

    /**
     * @var StorageContainerMaterialDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: StorageContainerMaterialDTO::class)))]
    private array $storageContainerMaterials;

    /**
     * @param StorageContainerSummaryDTO[] $storageContainers
     * @param StorageContainerMaterialDTO[] $storageContainerMaterials
     */
    public function __construct(
        string $id,
        ?string $recycledAt,
        ?string $recycledByUserId,
        string $createdAt,
        string $updatedAt,
        array $storageContainers,
        array $storageContainerMaterials,
    ) {
        $this->id = $id;
        $this->recycledAt = $recycledAt;
        $this->recycledByUserId = $recycledByUserId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->storageContainers = $storageContainers;
        $this->storageContainerMaterials = $storageContainerMaterials;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRecycledAt(): ?string
    {
        return $this->recycledAt;
    }

    public function getRecycledByUserId(): ?string
    {
        return $this->recycledByUserId;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * @return StorageContainerSummaryDTO[]
     */
    public function getStorageContainers(): array
    {
        return $this->storageContainers;
    }

    /**
     * @return StorageContainerMaterialDTO[]
     */
    public function getStorageContainerMaterials(): array
    {
        return $this->storageContainerMaterials;
    }
}
