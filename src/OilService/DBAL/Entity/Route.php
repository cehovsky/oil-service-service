<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Repository\RouteRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use App\Warehouse\DBAL\Entity\StorageContainerLocation;
use App\Warehouse\DBAL\Entity\StorageContainerMaterial;
use App\OilService\DBAL\Entity\RouteUser;
use App\OilService\DBAL\Entity\Order;

#[ORM\Table(name: 'oil_service_route')]
#[ORM\Entity(repositoryClass: RouteRepository::class)]
class Route
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Car::class, inversedBy: 'routes')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Car $car;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isActive;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $date;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    /** @var Collection<int, Term> */
    #[ORM\ManyToMany(targetEntity: Term::class, inversedBy: 'routes')]
    #[ORM\JoinTable(name: 'oil_service_route_term')]
    private Collection $terms;

    /** @var Collection<int, Order> */
    #[ORM\OneToMany(mappedBy: 'route', targetEntity: Order::class)]
    private Collection $orders;

    /** @var Collection<int, StorageContainerLocation> */
    #[ORM\OneToMany(mappedBy: 'route', targetEntity: StorageContainerLocation::class)]
    private Collection $storageContainerLocations;

    /** @var Collection<int, RouteUser> */
    #[ORM\OneToMany(mappedBy: 'route', targetEntity: RouteUser::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $routeUsers;

    /** @var Collection<int, StorageContainerMaterial> */
    #[ORM\OneToMany(mappedBy: 'route', targetEntity: StorageContainerMaterial::class)]
    private Collection $storageContainerMaterials;

    public function __construct(
        Uuid $id,
        ?Car $car,
        bool $isActive,
        DateTimeImmutable $date,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->car = $car;
        $this->isActive = $isActive;
        $this->date = $date;
        $this->createdAt = $createdAt;
        $this->terms = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->storageContainerLocations = new ArrayCollection();
        $this->routeUsers = new ArrayCollection();
        $this->storageContainerMaterials = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): self
    {
        $this->car = $car;

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

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Term>
     */
    public function getTerms(): Collection
    {
        return $this->terms;
    }

    public function addTerm(Term $term): self
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
            $term->addRoute($this);
        }

        return $this;
    }

    public function removeTerm(Term $term): self
    {
        if ($this->terms->removeElement($term)) {
            $term->removeRoute($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setRoute($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getRoute() === $this) {
                $order->setRoute(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StorageContainerLocation>
     */
    public function getStorageContainerLocations(): Collection
    {
        return $this->storageContainerLocations;
    }

    public function addStorageContainerLocation(StorageContainerLocation $location): self
    {
        if (!$this->storageContainerLocations->contains($location)) {
            $this->storageContainerLocations->add($location);
            $location->setRoute($this);
        }

        return $this;
    }

    public function removeStorageContainerLocation(StorageContainerLocation $location): self
    {
        if ($this->storageContainerLocations->removeElement($location)) {
            // unlink owning side if still linked
            if ($location->getRoute() === $this) {
                $location->clearRoute();
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RouteUser>
     */
    public function getRouteUsers(): Collection
    {
        return $this->routeUsers;
    }

    public function addRouteUser(RouteUser $routeUser): self
    {
        if (!$this->routeUsers->contains($routeUser)) {
            $this->routeUsers->add($routeUser);
            $routeUser->setRoute($this);
        }

        return $this;
    }

    public function removeRouteUser(RouteUser $routeUser): self
    {
        $this->routeUsers->removeElement($routeUser);

        return $this;
    }

    /**
     * @return Collection<int, StorageContainerMaterial>
     */
    public function getStorageContainerMaterials(): Collection
    {
        return $this->storageContainerMaterials;
    }

    public function addStorageContainerMaterial(StorageContainerMaterial $storageContainerMaterial): self
    {
        if (!$this->storageContainerMaterials->contains($storageContainerMaterial)) {
            $this->storageContainerMaterials->add($storageContainerMaterial);
            $storageContainerMaterial->setRoute($this);
        }

        return $this;
    }

    public function removeStorageContainerMaterial(StorageContainerMaterial $storageContainerMaterial): self
    {
        if ($this->storageContainerMaterials->removeElement($storageContainerMaterial)) {
            if ($storageContainerMaterial->getRoute() === $this) {
                $storageContainerMaterial->setRoute(null);
            }
        }

        return $this;
    }
}
