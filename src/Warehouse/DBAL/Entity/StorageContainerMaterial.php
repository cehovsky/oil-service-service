<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Entity;

use App\Auth\DBAL\Entity\User;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Entity\Route;
use App\Warehouse\DBAL\Repository\StorageContainerMaterialRepository;
use App\Warehouse\DBAL\Entity\Warehouse;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'warehouse_storage_container_material')]
#[ORM\Index(name: 'idx_container_recycled', columns: ['storage_container_id', 'is_recycled'])]
#[ORM\Index(name: 'idx_warehouse', columns: ['warehouse_id'])]
#[ORM\Index(name: 'idx_route', columns: ['route_id'])]
#[ORM\Index(name: 'idx_order', columns: ['order_id'])]
#[ORM\Index(name: 'idx_recycling', columns: ['recycling_id'])]
#[ORM\Index(name: 'idx_created_at', columns: ['created_at'])]
#[ORM\Entity(repositoryClass: StorageContainerMaterialRepository::class)]
class StorageContainerMaterial
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: StorageContainer::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private StorageContainer $storageContainer;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: WasteMaterial::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private WasteMaterial $wasteMaterial;

    #[ORM\ManyToOne(targetEntity: Warehouse::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Warehouse $warehouse;

    #[ORM\ManyToOne(targetEntity: Route::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Route $route;

    #[ORM\ManyToOne(targetEntity: Recycling::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Recycling $recycling;

    #[ORM\ManyToOne(targetEntity: Order::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Order $order;

    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::FLOAT)]
    private float $volume;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isRecycled;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private User $createdBy;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private User $updatedBy;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, StorageContainerMaterialHistory>&Selectable<int, StorageContainerMaterialHistory> */
    #[ORM\OneToMany(mappedBy: 'storageContainerMaterial', targetEntity: StorageContainerMaterialHistory::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $history;

    public function __construct(
        Uuid $id,
        StorageContainer $storageContainer,
        WasteMaterial $wasteMaterial,
        float $volume,
        bool $isRecycled,
        User $createdBy,
        User $updatedBy,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        ?Warehouse $warehouse = null,
        ?Route $route = null,
        ?Recycling $recycling = null,
        ?Order $order = null,
    ) {
        $this->assertSingleOrigin($warehouse, $route);

        $this->id = $id;
        $this->storageContainer = $storageContainer;
        $this->wasteMaterial = $wasteMaterial;
        $this->volume = $volume;
        $this->isRecycled = $isRecycled;
        $this->createdBy = $createdBy;
        $this->updatedBy = $updatedBy;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->warehouse = $warehouse;
        $this->route = $route;
        $this->recycling = $recycling;
        $this->order = $order;
        $this->history = new ArrayCollection();
    }

    private function assertSingleOrigin(?Warehouse $warehouse, ?Route $route): void
    {
        if ($warehouse !== null && $route !== null) {
            throw new InvalidArgumentException('Storage container material cannot reference a warehouse and a route at the same time.');
        }
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getStorageContainer(): StorageContainer
    {
        return $this->storageContainer;
    }

    public function setStorageContainer(StorageContainer $storageContainer): self
    {
        $this->storageContainer = $storageContainer;

        return $this;
    }

    public function getWasteMaterial(): WasteMaterial
    {
        return $this->wasteMaterial;
    }

    public function setWasteMaterial(WasteMaterial $wasteMaterial): self
    {
        $this->wasteMaterial = $wasteMaterial;

        return $this;
    }

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(?Warehouse $warehouse): self
    {
        $this->assertSingleOrigin($warehouse, $this->route);

        $this->warehouse = $warehouse;

        if ($warehouse !== null) {
            $this->route = null;
        }

        return $this;
    }

    public function getRoute(): ?Route
    {
        return $this->route;
    }

    public function setRoute(?Route $route): self
    {
        $this->assertSingleOrigin($this->warehouse, $route);

        $this->route = $route;

        if ($route !== null) {
            $this->warehouse = null;
        }

        return $this;
    }

    public function getOrigin(): Warehouse|Route|null
    {
        return $this->warehouse ?? $this->route;
    }

    public function getRecycling(): ?Recycling
    {
        return $this->recycling;
    }

    public function setRecycling(?Recycling $recycling): self
    {
        $this->recycling = $recycling;

        return $this;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        if ($this->order === $order) {
            return $this;
        }

        if ($this->order !== null) {
            $this->order->getStorageContainerMaterials()->removeElement($this);
        }

        $this->order = $order;

        if ($order !== null && !$order->getStorageContainerMaterials()->contains($this)) {
            $order->addStorageContainerMaterial($this);
        }

        return $this;
    }

    public function getVolume(): float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getIsRecycled(): bool
    {
        return $this->isRecycled;
    }

    public function setIsRecycled(bool $isRecycled): self
    {
        $this->isRecycled = $isRecycled;

        return $this;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

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
     * @return Collection<int, StorageContainerMaterialHistory>&Selectable<int, StorageContainerMaterialHistory>
     */
    public function getHistory(): Collection
    {
        return $this->history;
    }

    public function addHistory(StorageContainerMaterialHistory $history): self
    {
        if (!$this->history->contains($history)) {
            $this->history->add($history);
        }

        return $this;
    }

    public function removeHistory(StorageContainerMaterialHistory $history): self
    {
        $this->history->removeElement($history);

        return $this;
    }
}
