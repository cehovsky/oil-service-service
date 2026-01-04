<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class StorageContainerDTO
{
    #[OA\Property(example: 'c2a63a4d-46b4-4d3b-98e3-d7343776e0b1')]
    private string $id;

    #[OA\Property(example: 'SC-001')]
    private string $code;

    #[OA\Property(example: 'Container for used oil', nullable: true)]
    private ?string $description;

    #[OA\Property(example: true)]
    private bool $isActive;

    #[OA\Property(example: 'barrel')]
    private string $type;

    #[OA\Property(example: 200.0)]
    private float $capacity;

    #[OA\Property(example: 'l')]
    private string $volumeUnit;

    #[OA\Property(example: '2026-01-02T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-01-03T12:00:00+00:00')]
    private string $updatedAt;

    /**
     * @var WasteMaterialSummaryDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: WasteMaterialSummaryDTO::class)))]
    private array $preferredWasteMaterials;

    #[OA\Property(ref: new Model(type: StorageContainerActualLocationDTO::class), nullable: true)]
    private ?StorageContainerActualLocationDTO $actualLocation;

    /**
     * @param WasteMaterialSummaryDTO[] $preferredWasteMaterials
     */
    public function __construct(
        string $id,
        string $code,
        ?string $description,
        bool $isActive,
        string $type,
        float $capacity,
        string $volumeUnit,
        string $createdAt,
        string $updatedAt,
        array $preferredWasteMaterials,
        ?StorageContainerActualLocationDTO $actualLocation,
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->description = $description;
        $this->isActive = $isActive;
        $this->type = $type;
        $this->capacity = $capacity;
        $this->volumeUnit = $volumeUnit;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->preferredWasteMaterials = $preferredWasteMaterials;
        $this->actualLocation = $actualLocation;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCapacity(): float
    {
        return $this->capacity;
    }

    public function getVolumeUnit(): string
    {
        return $this->volumeUnit;
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
     * @return WasteMaterialSummaryDTO[]
     */
    public function getPreferredWasteMaterials(): array
    {
        return $this->preferredWasteMaterials;
    }

    public function getActualLocation(): ?StorageContainerActualLocationDTO
    {
        return $this->actualLocation;
    }
}
