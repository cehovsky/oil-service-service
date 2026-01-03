<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Entity;

use App\OilService\DBAL\Entity\Route;
use App\Warehouse\DBAL\Repository\StorageContainerLocationRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'warehouse_storage_container_location')]
#[ORM\Entity(repositoryClass: StorageContainerLocationRepository::class)]
class StorageContainerLocation
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: StorageContainer::class, inversedBy: 'locations', fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private StorageContainer $storageContainer;

    #[ORM\ManyToOne(targetEntity: Warehouse::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Warehouse $warehouse;

    #[ORM\ManyToOne(targetEntity: Route::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Route $route;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $movedAt;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid $id,
        StorageContainer $storageContainer,
        DateTimeImmutable $movedAt,
        ?Warehouse $warehouse,
        ?Route $route,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->assertSingleLocation($warehouse, $route);

        $this->id = $id;
        $this->storageContainer = $storageContainer;
        $this->warehouse = $warehouse;
        $this->route = $route;
        $this->movedAt = $movedAt;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    private function assertSingleLocation(?Warehouse $warehouse, ?Route $route): void
    {
        if ($warehouse === null && $route === null) {
            throw new InvalidArgumentException('Storage container location must reference a warehouse or a route.');
        }

        if ($warehouse !== null && $route !== null) {
            throw new InvalidArgumentException('Storage container location cannot reference a warehouse and a route at the same time.');
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

    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(Warehouse $warehouse): self
    {
        $this->assertSingleLocation($warehouse, null);

        $this->warehouse = $warehouse;
        $this->route = null;

        return $this;
    }

    public function getRoute(): ?Route
    {
        return $this->route;
    }

    public function setRoute(Route $route): self
    {
        $this->assertSingleLocation(null, $route);

        $this->route = $route;
        $this->warehouse = null;

        return $this;
    }

    public function getLocation(): Warehouse|Route
    {
        return $this->warehouse ?? $this->route;
    }

    public function getMovedAt(): DateTimeImmutable
    {
        return $this->movedAt;
    }

    public function setMovedAt(DateTimeImmutable $movedAt): self
    {
        $this->movedAt = $movedAt;

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
