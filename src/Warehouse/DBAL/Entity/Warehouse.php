<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Entity;

use App\Warehouse\DBAL\Repository\WarehouseRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'warehouse_warehouse')]
#[ORM\Entity(repositoryClass: WarehouseRepository::class)]
class Warehouse
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private string $label;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private string $shortLabel;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $address;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isActive;

    #[Assert\Range(min: -90, max: 90)]
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $latitude = null;

    #[Assert\Range(min: -180, max: 180)]
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $longitude = null;

    #[Assert\NotNull]
    #[ORM\Column(options: ['default' => false])]
    private bool $isGarage;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, StorageContainerLocation> */
    #[ORM\OneToMany(mappedBy: 'warehouse', targetEntity: StorageContainerLocation::class)]
    private Collection $storageContainerLocations;

    public function __construct(
        Uuid $id,
        string $label,
        string $shortLabel,
        ?string $address,
        bool $isActive,
        ?float $latitude,
        ?float $longitude,
        bool $isGarage,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->shortLabel = $shortLabel;
        $this->address = $address;
        $this->isActive = $isActive;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->isGarage = $isGarage;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->storageContainerLocations = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getIsGarage(): bool
    {
        return $this->isGarage;
    }

    public function setIsGarage(bool $isGarage): self
    {
        $this->isGarage = $isGarage;

        return $this;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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
            $location->setWarehouse($this);
        }

        return $this;
    }

    public function removeStorageContainerLocation(StorageContainerLocation $location): self
    {
        if ($this->storageContainerLocations->removeElement($location)) {
            // unlink owning side if still linked
            if ($location->getWarehouse() === $this) {
                $location->clearWarehouse();
            }
        }

        return $this;
    }
}
