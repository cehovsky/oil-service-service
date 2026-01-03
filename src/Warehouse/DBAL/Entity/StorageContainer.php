<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Entity;

use App\Warehouse\DBAL\Enum\StorageContainerTypeEnum;
use App\Warehouse\DBAL\Enum\StorageVolumeUnitEnum;
use App\Warehouse\DBAL\Repository\StorageContainerRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'warehouse_storage_container')]
#[ORM\Entity(repositoryClass: StorageContainerRepository::class)]
class StorageContainer
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, unique: true)]
    private string $code;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isActive;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: StorageContainerTypeEnum::class, length: 64)]
    private StorageContainerTypeEnum $type;

    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::FLOAT)]
    private float $capacity;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: StorageVolumeUnitEnum::class, length: 8)]
    private StorageVolumeUnitEnum $volumeUnit;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid $id,
        string $code,
        ?string $description,
        bool $isActive,
        StorageContainerTypeEnum $type,
        float $capacity,
        StorageVolumeUnitEnum $volumeUnit,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
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
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getType(): StorageContainerTypeEnum
    {
        return $this->type;
    }

    public function setType(StorageContainerTypeEnum $type): self
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

    public function getVolumeUnit(): StorageVolumeUnitEnum
    {
        return $this->volumeUnit;
    }

    public function setVolumeUnit(StorageVolumeUnitEnum $volumeUnit): self
    {
        $this->volumeUnit = $volumeUnit;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
