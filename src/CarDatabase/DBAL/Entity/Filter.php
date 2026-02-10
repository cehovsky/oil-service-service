<?php

declare(strict_types=1);

namespace App\CarDatabase\DBAL\Entity;

use App\CarDatabase\DBAL\Enum\FilterManufacturerEnum;
use App\CarDatabase\DBAL\Enum\FilterTypeEnum;
use App\CarDatabase\DBAL\Repository\FilterRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'car_database_filter')]
#[ORM\Index(name: 'idx_filter_type', columns: ['filter_type'])]
#[ORM\Index(name: 'idx_filter_manufacturer', columns: ['manufacturer'])]
#[ORM\Index(name: 'idx_filter_code', columns: ['code'])]
#[ORM\Index(name: 'idx_filter_oem_code', columns: ['oem_code'])]
#[ORM\Entity(repositoryClass: FilterRepository::class)]
class Filter
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotNull]
    #[ORM\Column(name: 'filter_type', type: Types::STRING, enumType: FilterTypeEnum::class, length: 32)]
    private FilterTypeEnum $filterType;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private string $manufacturer;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private string $code;

    #[Assert\Length(max: 255)]
    #[ORM\Column(name: 'oem_code', length: 255, nullable: true)]
    private ?string $oemCode = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $thread = null;

    #[ORM\Column(name: 'height_mm', type: Types::INTEGER, nullable: true)]
    private ?int $heightMm = null;

    #[ORM\Column(name: 'diameter_mm', type: Types::INTEGER, nullable: true)]
    private ?int $diameterMm = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, EngineFilter> */
    #[ORM\OneToMany(mappedBy: 'filter', targetEntity: EngineFilter::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $engineFilters;

    public function __construct(
        Uuid $id,
        FilterTypeEnum $filterType,
        string $manufacturer,
        string $code,
        ?string $oemCode,
        ?string $thread,
        ?int $heightMm,
        ?int $diameterMm,
        ?string $notes,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->filterType = $filterType;
        $this->manufacturer = $manufacturer;
        $this->code = $code;
        $this->oemCode = $oemCode;
        $this->thread = $thread;
        $this->heightMm = $heightMm;
        $this->diameterMm = $diameterMm;
        $this->notes = $notes;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->engineFilters = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getFilterType(): FilterTypeEnum
    {
        return $this->filterType;
    }

    public function setFilterType(FilterTypeEnum $filterType): self
    {
        $this->filterType = $filterType;

        return $this;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getOemCode(): ?string
    {
        return $this->oemCode;
    }

    public function setOemCode(?string $oemCode): self
    {
        $this->oemCode = $oemCode;

        return $this;
    }

    public function getThread(): ?string
    {
        return $this->thread;
    }

    public function setThread(?string $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    public function getHeightMm(): ?int
    {
        return $this->heightMm;
    }

    public function setHeightMm(?int $heightMm): self
    {
        $this->heightMm = $heightMm;

        return $this;
    }

    public function getDiameterMm(): ?int
    {
        return $this->diameterMm;
    }

    public function setDiameterMm(?int $diameterMm): self
    {
        $this->diameterMm = $diameterMm;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

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

    /** @return Collection<int, EngineFilter> */
    public function getEngineFilters(): Collection
    {
        return $this->engineFilters;
    }
}
