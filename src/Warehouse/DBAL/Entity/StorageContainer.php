<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Entity;

use App\Warehouse\DBAL\Enum\StorageContainerTypeEnum;
use App\Warehouse\DBAL\Enum\VolumeUnitEnum;
use App\Warehouse\DBAL\Repository\StorageContainerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use DateTimeImmutable;
use Doctrine\Common\Collections\Order;
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
    #[ORM\Column(type: Types::STRING, enumType: VolumeUnitEnum::class, length: 8)]
    private VolumeUnitEnum $volumeUnit;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, StorageContainerLocation>&Selectable<int, StorageContainerLocation> */
    #[ORM\OneToMany(mappedBy: 'storageContainer', targetEntity: StorageContainerLocation::class, fetch: 'EXTRA_LAZY')]
    #[ORM\OrderBy(['movedAt' => 'ASC'])]
    private Collection $locations;

    /** @var Collection<int, WasteMaterial>&Selectable<int, WasteMaterial> */
    #[ORM\ManyToMany(targetEntity: WasteMaterial::class, inversedBy: 'preferredStorageContainers', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(
        name: 'warehouse_storage_container_preferred_waste_materials',
        joinColumns: [new ORM\JoinColumn(name: 'storage_container_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'waste_material_id', referencedColumnName: 'id')]
    )]
    private Collection $preferredWasteMaterials;

    public function __construct(
        Uuid $id,
        string $code,
        ?string $description,
        bool $isActive,
        StorageContainerTypeEnum $type,
        float $capacity,
        VolumeUnitEnum $volumeUnit,
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
        $this->locations = new ArrayCollection();
        $this->preferredWasteMaterials = new ArrayCollection();
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
     * @return Collection<int, StorageContainerLocation>&Selectable<int, StorageContainerLocation>
     */
    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function addLocation(StorageContainerLocation $location): self
    {
        if (!$this->locations->contains($location)) {
            $this->locations->add($location);
            $location->setStorageContainer($this);
        }

        return $this;
    }

    public function removeLocation(StorageContainerLocation $location): self
    {
        $this->locations->removeElement($location);

        return $this;
    }

    public function actualLocation(?DateTimeImmutable $asOf = null): ?StorageContainerLocation
    {
        $asOf ??= new DateTimeImmutable();

        $criteria = Criteria::create()
            ->where(Criteria::expr()->lte('movedAt', $asOf))
            ->orderBy(['movedAt' => Order::Descending])
            ->setMaxResults(1);

        $matches = $this->locations->matching($criteria);

        $location = $matches->first();

        if ($location === false) {
            return null;
        }

        return $location;
    }

    /**
     * @return Collection<int, StorageContainerLocation>&Selectable<int, StorageContainerLocation>
     */
    public function locationHistory(): Collection
    {
        $criteria = Criteria::create()->orderBy(['movedAt' => Order::Ascending]);

        return $this->locations->matching($criteria);
    }

    /**
     * @return Collection<int, WasteMaterial>&Selectable<int, WasteMaterial>
     */
    public function getPreferredWasteMaterials(): Collection
    {
        return $this->preferredWasteMaterials;
    }

    public function addPreferredWasteMaterial(WasteMaterial $wasteMaterial): self
    {
        if (!$this->preferredWasteMaterials->contains($wasteMaterial)) {
            $this->preferredWasteMaterials->add($wasteMaterial);
        }

        return $this;
    }

    public function removePreferredWasteMaterial(WasteMaterial $wasteMaterial): self
    {
        $this->preferredWasteMaterials->removeElement($wasteMaterial);

        return $this;
    }
}
