<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Entity;

use App\Auth\DBAL\Entity\User;
use App\Warehouse\DBAL\Entity\StorageContainer;
use App\Warehouse\DBAL\Entity\StorageContainerMaterial;
use App\Warehouse\DBAL\Repository\RecyclingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'warehouse_recycling')]
#[ORM\Index(name: 'idx_recycled_at', columns: ['recycled_at'])]
#[ORM\Index(name: 'idx_recycled_by', columns: ['recycled_by_id'])]
#[ORM\Entity(repositoryClass: RecyclingRepository::class)]
class Recycling
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $recycledAt;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $recycledBy;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, StorageContainerMaterial>&Selectable<int, StorageContainerMaterial> */
    #[ORM\OneToMany(mappedBy: 'recycling', targetEntity: StorageContainerMaterial::class, fetch: 'EXTRA_LAZY')]
    private Collection $storageContainerMaterials;

    /** @var Collection<int, StorageContainer>&Selectable<int, StorageContainer> */
    #[ORM\ManyToMany(targetEntity: StorageContainer::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(
        name: 'warehouse_recycling_storage_containers',
        joinColumns: [new ORM\JoinColumn(name: 'recycling_id', referencedColumnName: 'id')],
        inverseJoinColumns: [new ORM\JoinColumn(name: 'storage_container_id', referencedColumnName: 'id')],
    )]
    private Collection $storageContainers;

    public function __construct(
        Uuid $id,
        ?DateTimeImmutable $recycledAt,
        ?User $recycledBy,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->recycledAt = $recycledAt;
        $this->recycledBy = $recycledBy;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->storageContainerMaterials = new ArrayCollection();
        $this->storageContainers = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRecycledAt(): ?DateTimeImmutable
    {
        return $this->recycledAt;
    }

    public function setRecycledAt(?DateTimeImmutable $recycledAt): self
    {
        $this->recycledAt = $recycledAt;

        return $this;
    }

    public function getRecycledBy(): ?User
    {
        return $this->recycledBy;
    }

    public function setRecycledBy(?User $recycledBy): self
    {
        $this->recycledBy = $recycledBy;

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
     * @return Collection<int, StorageContainerMaterial>&Selectable<int, StorageContainerMaterial>
     */
    public function getStorageContainerMaterials(): Collection
    {
        return $this->storageContainerMaterials;
    }

    public function addStorageContainerMaterial(StorageContainerMaterial $storageContainerMaterial): self
    {
        if (!$this->storageContainerMaterials->contains($storageContainerMaterial)) {
            $this->storageContainerMaterials->add($storageContainerMaterial);
            $storageContainerMaterial->setRecycling($this);
        }

        return $this;
    }

    public function removeStorageContainerMaterial(StorageContainerMaterial $storageContainerMaterial): self
    {
        if ($this->storageContainerMaterials->removeElement($storageContainerMaterial)) {
            if ($storageContainerMaterial->getRecycling() === $this) {
                $storageContainerMaterial->setRecycling(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StorageContainer>&Selectable<int, StorageContainer>
     */
    public function getStorageContainers(): Collection
    {
        return $this->storageContainers;
    }

    public function addStorageContainer(StorageContainer $storageContainer): self
    {
        if (!$this->storageContainers->contains($storageContainer)) {
            $this->storageContainers->add($storageContainer);
        }

        return $this;
    }

    public function removeStorageContainer(StorageContainer $storageContainer): self
    {
        $this->storageContainers->removeElement($storageContainer);

        return $this;
    }
}
