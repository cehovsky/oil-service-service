<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Modules\Warehouse\Validation\Constraint\UniqueStorageContainerCode;
use App\Warehouse\DBAL\Enum\StorageContainerTypeEnum;
use App\Warehouse\DBAL\Enum\VolumeUnitEnum;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueStorageContainerCode]
class StorageContainerCreateRequestDTO
{
    #[OA\Property(example: 'SC-001')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $code;

    #[OA\Property(example: 'barrel')]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [StorageContainerTypeEnum::class, 'values'])]
    private string $type;

    #[OA\Property(example: 200.0)]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private float $capacity;

    #[OA\Property(example: 'l')]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [VolumeUnitEnum::class, 'values'])]
    private string $volumeUnit;

    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isActive;

    #[OA\Property(example: 'Container for used oil', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $description = null;

    /**
     * @var string[]|null
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string', example: '1f391a20-4412-4bb3-99c6-873f1e0c1234'), nullable: true)]
    #[Assert\All([
        new Assert\Uuid(),
    ])]
    private ?array $preferredWasteMaterialIds = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCapacity(): float
    {
        return $this->capacity;
    }

    public function setCapacity(float $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getVolumeUnit(): string
    {
        return $this->volumeUnit;
    }

    public function setVolumeUnit(string $volumeUnit): self
    {
        $this->volumeUnit = $volumeUnit;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getPreferredWasteMaterialIds(): ?array
    {
        return $this->preferredWasteMaterialIds;
    }

    /**
     * @param string[]|null $preferredWasteMaterialIds
     */
    public function setPreferredWasteMaterialIds(?array $preferredWasteMaterialIds): self
    {
        $this->preferredWasteMaterialIds = $preferredWasteMaterialIds;

        return $this;
    }
}
