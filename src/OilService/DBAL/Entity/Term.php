<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Repository\TermRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_term')]
#[ORM\Index(name: 'idx_date_timeslot', columns: ['date', 'time_slot'])]
#[ORM\Index(name: 'idx_active_date', columns: ['is_active', 'date'])]
#[ORM\UniqueConstraint(name: 'uniq_date_timeslot', columns: ['date', 'time_slot'])]
#[ORM\Entity(repositoryClass: TermRepository::class)]
class Term
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $date;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: RealizationTimeSlotEnum::class, length: 32)]
    private RealizationTimeSlotEnum $timeSlot;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isActive;

    #[Assert\NotNull]
    #[Assert\Positive]
    #[ORM\Column(type: Types::INTEGER)]
    private int $maxCount;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    /** @var Collection<int, Route> */
    #[ORM\ManyToMany(targetEntity: Route::class, mappedBy: 'terms')]
    private Collection $routes;

    public function __construct(
        Uuid $id,
        DateTimeImmutable $date,
        RealizationTimeSlotEnum $timeSlot,
        bool $isActive,
        int $maxCount,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->timeSlot = $timeSlot;
        $this->isActive = $isActive;
        $this->maxCount = $maxCount;
        $this->createdAt = $createdAt;
        $this->routes = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public function getTimeSlot(): RealizationTimeSlotEnum
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(RealizationTimeSlotEnum $timeSlot): self
    {
        $this->timeSlot = $timeSlot;

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

    public function getMaxCount(): int
    {
        return $this->maxCount;
    }

    public function setMaxCount(int $maxCount): self
    {
        $this->maxCount = $maxCount;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Route>
     */
    public function getRoutes(): Collection
    {
        return $this->routes;
    }

    public function addRoute(Route $route): self
    {
        if (!$this->routes->contains($route)) {
            $this->routes->add($route);
            $route->addTerm($this);
        }

        return $this;
    }

    public function removeRoute(Route $route): self
    {
        if ($this->routes->removeElement($route)) {
            $route->removeTerm($this);
        }

        return $this;
    }
}
