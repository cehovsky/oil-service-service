<?php

declare(strict_types=1);

namespace App\CarDatabase\DBAL\Entity;

use App\CarDatabase\DBAL\Repository\EngineFilterRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'car_database_engine_filter')]
#[ORM\UniqueConstraint(name: 'car_database_engine_filter_unique', columns: ['engine_id', 'filter_id'])]
#[ORM\Index(name: 'idx_engine_filter_engine', columns: ['engine_id'])]
#[ORM\Index(name: 'idx_engine_filter_filter', columns: ['filter_id'])]
#[ORM\Entity(repositoryClass: EngineFilterRepository::class)]
class EngineFilter
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Engine::class, inversedBy: 'engineFilters', fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Engine $engine;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Filter::class, inversedBy: 'engineFilters', fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Filter $filter;

    #[ORM\Column(name: 'is_primary')]
    private bool $isPrimary;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $source = null;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid $id,
        Engine $engine,
        Filter $filter,
        bool $isPrimary,
        ?string $source,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->engine = $engine;
        $this->filter = $filter;
        $this->isPrimary = $isPrimary;
        $this->source = $source;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getEngine(): Engine
    {
        return $this->engine;
    }

    public function setEngine(Engine $engine): self
    {
        $this->engine = $engine;

        return $this;
    }

    public function getFilter(): Filter
    {
        return $this->filter;
    }

    public function setFilter(Filter $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): self
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

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
