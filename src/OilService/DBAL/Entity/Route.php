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

    /** @var Collection<int, Form> */
    #[ORM\OneToMany(mappedBy: 'route', targetEntity: Form::class)]
    private Collection $forms;

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
        $this->forms = new ArrayCollection();
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
     * @return Collection<int, Form>
     */
    public function getForms(): Collection
    {
        return $this->forms;
    }
}
