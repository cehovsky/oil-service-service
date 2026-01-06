<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Entity;

use App\Auth\DBAL\Entity\User;
use App\Warehouse\DBAL\Repository\RecyclingRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'warehouse_recycling')]
#[ORM\Entity(repositoryClass: RecyclingRepository::class)]
class Recycling
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $recycledAt;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private User $recycledBy;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid $id,
        DateTimeImmutable $recycledAt,
        User $recycledBy,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->recycledAt = $recycledAt;
        $this->recycledBy = $recycledBy;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRecycledAt(): DateTimeImmutable
    {
        return $this->recycledAt;
    }

    public function setRecycledAt(DateTimeImmutable $recycledAt): self
    {
        $this->recycledAt = $recycledAt;

        return $this;
    }

    public function getRecycledBy(): User
    {
        return $this->recycledBy;
    }

    public function setRecycledBy(User $recycledBy): self
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
}
