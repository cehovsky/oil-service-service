<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Entity;

use App\Warehouse\DBAL\Enum\VolumeUnitEnum;
use App\Warehouse\DBAL\Repository\WasteMaterialRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'warehouse_waste_material')]
#[ORM\Entity(repositoryClass: WasteMaterialRepository::class)]
class WasteMaterial
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, unique: true)]
    private string $code;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private string $label;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private string $shortLabel;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isActive;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: VolumeUnitEnum::class, length: 8)]
    private VolumeUnitEnum $volumeUnit;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, StorageContainer>&Selectable */
    #[ORM\ManyToMany(targetEntity: StorageContainer::class, mappedBy: 'preferredWasteMaterials', fetch: 'EXTRA_LAZY')]
    private Collection $preferredStorageContainers;

    public function __construct(
        Uuid $id,
        string $code,
        string $label,
        string $shortLabel,
        bool $isActive,
        VolumeUnitEnum $volumeUnit,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->label = $label;
        $this->shortLabel = $shortLabel;
        $this->isActive = $isActive;
        $this->volumeUnit = $volumeUnit;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->preferredStorageContainers = new ArrayCollection();
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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getShortLabel(): string
    {
        return $this->shortLabel;
    }

    public function setShortLabel(string $shortLabel): self
    {
        $this->shortLabel = $shortLabel;

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

    public function getVolumeUnit(): VolumeUnitEnum
    {
        return $this->volumeUnit;
    }

    public function setVolumeUnit(VolumeUnitEnum $volumeUnit): self
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

    /**
     * @return Collection<int, StorageContainer>&Selectable
     */
    public function getPreferredStorageContainers(): Collection
    {
        return $this->preferredStorageContainers;
    }

    public function addPreferredStorageContainer(StorageContainer $storageContainer): self
    {
        if (!$this->preferredStorageContainers->contains($storageContainer)) {
            $this->preferredStorageContainers->add($storageContainer);
            $storageContainer->addPreferredWasteMaterial($this);
        }

        return $this;
    }

    public function removePreferredStorageContainer(StorageContainer $storageContainer): self
    {
        if ($this->preferredStorageContainers->removeElement($storageContainer)) {
            $storageContainer->removePreferredWasteMaterial($this);
        }

        return $this;
    }
}
