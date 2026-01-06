<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Entity;

use App\Auth\DBAL\Entity\User;
use App\Warehouse\DBAL\Repository\StorageContainerMaterialHistoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'warehouse_storage_container_material_history')]
#[ORM\Entity(repositoryClass: StorageContainerMaterialHistoryRepository::class)]
class StorageContainerMaterialHistory
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: StorageContainerMaterial::class, inversedBy: 'history', fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private StorageContainerMaterial $storageContainerMaterial;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: StorageContainer::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private StorageContainer $storageContainer;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private User $createdBy;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(
        Uuid $id,
        StorageContainerMaterial $storageContainerMaterial,
        StorageContainer $storageContainer,
        User $createdBy,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->storageContainerMaterial = $storageContainerMaterial;
        $this->storageContainer = $storageContainer;
        $this->createdBy = $createdBy;
        $this->createdAt = $createdAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getStorageContainerMaterial(): StorageContainerMaterial
    {
        return $this->storageContainerMaterial;
    }

    public function getStorageContainer(): StorageContainer
    {
        return $this->storageContainer;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
