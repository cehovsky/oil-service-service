<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Enum\CarStatusEnum;
use App\OilService\DBAL\Repository\CarRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_car')]
#[ORM\Entity(repositoryClass: CarRepository::class)]
class Car
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private string $label;

    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    #[ORM\Column(length: 10, unique: true)]
    private string $ident;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20)]
    private string $licensePlate;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: CarStatusEnum::class, length: 32)]
    private CarStatusEnum $status;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    /** @var Collection<int, Route> */
    #[ORM\OneToMany(mappedBy: 'car', targetEntity: Route::class)]
    private Collection $routes;

    public function __construct(
        Uuid $id,
        string $label,
        string $ident,
        string $licensePlate,
        CarStatusEnum $status,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->ident = $ident;
        $this->licensePlate = $licensePlate;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->routes = new ArrayCollection();
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

    public function getIdent(): string
    {
        return $this->ident;
    }

    public function setIdent(string $ident): self
    {
        $this->ident = $ident;

        return $this;
    }

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): self
    {
        $this->licensePlate = $licensePlate;

        return $this;
    }

    public function getStatus(): CarStatusEnum
    {
        return $this->status;
    }

    public function setStatus(CarStatusEnum $status): self
    {
        $this->status = $status;

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
}
