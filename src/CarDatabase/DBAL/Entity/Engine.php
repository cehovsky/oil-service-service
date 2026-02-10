<?php

declare(strict_types=1);

namespace App\CarDatabase\DBAL\Entity;

use App\CarDatabase\DBAL\Repository\EngineRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'car_database_engine')]
#[ORM\Index(name: 'idx_engine_manufacturer', columns: ['manufacturer'])]
#[ORM\Index(name: 'idx_engine_model', columns: ['model'])]
#[ORM\Index(name: 'idx_engine_code', columns: ['engine_code'])]
#[ORM\Entity(repositoryClass: EngineRepository::class)]
class Engine
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    #[ORM\Column(length: 120)]
    private string $manufacturer;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private string $model;

    #[Assert\Length(max: 64)]
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $generation = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(name: 'engine_code', length: 64, nullable: true)]
    private ?string $engineCode = null;

    #[Assert\Length(max: 64)]
    #[ORM\Column(name: 'engine_family', length: 64, nullable: true)]
    private ?string $engineFamily = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $displacementCc = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $powerKw = null;

    #[Assert\Length(max: 32)]
    #[ORM\Column(length: 32, nullable: true)]
    private ?string $fuel = null;

    #[Assert\Length(max: 32)]
    #[ORM\Column(name: 'emission_standard', length: 32, nullable: true)]
    private ?string $emissionStandard = null;

    #[ORM\Column(name: 'production_from_year', type: Types::INTEGER, nullable: true)]
    private ?int $productionFromYear = null;

    #[ORM\Column(name: 'production_to_year', type: Types::INTEGER, nullable: true)]
    private ?int $productionToYear = null;

    #[ORM\Column(name: 'oil_capacity_l', type: Types::DECIMAL, precision: 6, scale: 2, nullable: true)]
    private ?string $oilCapacityL = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(name: 'oil_capacity_note', length: 255, nullable: true)]
    private ?string $oilCapacityNote = null;

    #[Assert\Length(max: 32)]
    #[ORM\Column(name: 'oil_viscosity', length: 32, nullable: true)]
    private ?string $oilViscosity = null;

    #[Assert\Length(max: 128)]
    #[ORM\Column(name: 'oil_specification', length: 128, nullable: true)]
    private ?string $oilSpecification = null;

    #[ORM\Column(name: 'oil_interval_km', type: Types::INTEGER, nullable: true)]
    private ?int $oilIntervalKm = null;

    #[ORM\Column(name: 'oil_interval_months', type: Types::INTEGER, nullable: true)]
    private ?int $oilIntervalMonths = null;

    #[ORM\Column(name: 'oil_drain_plug_torque_nm', type: Types::INTEGER, nullable: true)]
    private ?int $oilDrainPlugTorqueNm = null;

    #[ORM\Column(name: 'oil_filter_torque_nm', type: Types::INTEGER, nullable: true)]
    private ?int $oilFilterTorqueNm = null;

    #[ORM\Column(name: 'spark_plug_torque_nm', type: Types::INTEGER, nullable: true)]
    private ?int $sparkPlugTorqueNm = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $source = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $confidence = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, EngineFilter> */
    #[ORM\OneToMany(mappedBy: 'engine', targetEntity: EngineFilter::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $engineFilters;

    public function __construct(
        Uuid $id,
        string $manufacturer,
        string $model,
        ?string $generation,
        ?string $engineCode,
        ?string $engineFamily,
        ?int $displacementCc,
        ?int $powerKw,
        ?string $fuel,
        ?string $emissionStandard,
        ?int $productionFromYear,
        ?int $productionToYear,
        ?string $oilCapacityL,
        ?string $oilCapacityNote,
        ?string $oilViscosity,
        ?string $oilSpecification,
        ?int $oilIntervalKm,
        ?int $oilIntervalMonths,
        ?int $oilDrainPlugTorqueNm,
        ?int $oilFilterTorqueNm,
        ?int $sparkPlugTorqueNm,
        ?string $source,
        ?int $confidence,
        ?string $notes,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->manufacturer = $manufacturer;
        $this->model = $model;
        $this->generation = $generation;
        $this->engineCode = $engineCode;
        $this->engineFamily = $engineFamily;
        $this->displacementCc = $displacementCc;
        $this->powerKw = $powerKw;
        $this->fuel = $fuel;
        $this->emissionStandard = $emissionStandard;
        $this->productionFromYear = $productionFromYear;
        $this->productionToYear = $productionToYear;
        $this->oilCapacityL = $oilCapacityL;
        $this->oilCapacityNote = $oilCapacityNote;
        $this->oilViscosity = $oilViscosity;
        $this->oilSpecification = $oilSpecification;
        $this->oilIntervalKm = $oilIntervalKm;
        $this->oilIntervalMonths = $oilIntervalMonths;
        $this->oilDrainPlugTorqueNm = $oilDrainPlugTorqueNm;
        $this->oilFilterTorqueNm = $oilFilterTorqueNm;
        $this->sparkPlugTorqueNm = $sparkPlugTorqueNm;
        $this->source = $source;
        $this->confidence = $confidence;
        $this->notes = $notes;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->engineFilters = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getGeneration(): ?string
    {
        return $this->generation;
    }

    public function setGeneration(?string $generation): self
    {
        $this->generation = $generation;

        return $this;
    }

    public function getEngineCode(): ?string
    {
        return $this->engineCode;
    }

    public function setEngineCode(?string $engineCode): self
    {
        $this->engineCode = $engineCode;

        return $this;
    }

    public function getEngineFamily(): ?string
    {
        return $this->engineFamily;
    }

    public function setEngineFamily(?string $engineFamily): self
    {
        $this->engineFamily = $engineFamily;

        return $this;
    }

    public function getDisplacementCc(): ?int
    {
        return $this->displacementCc;
    }

    public function setDisplacementCc(?int $displacementCc): self
    {
        $this->displacementCc = $displacementCc;

        return $this;
    }

    public function getPowerKw(): ?int
    {
        return $this->powerKw;
    }

    public function setPowerKw(?int $powerKw): self
    {
        $this->powerKw = $powerKw;

        return $this;
    }

    public function getFuel(): ?string
    {
        return $this->fuel;
    }

    public function setFuel(?string $fuel): self
    {
        $this->fuel = $fuel;

        return $this;
    }

    public function getEmissionStandard(): ?string
    {
        return $this->emissionStandard;
    }

    public function setEmissionStandard(?string $emissionStandard): self
    {
        $this->emissionStandard = $emissionStandard;

        return $this;
    }

    public function getProductionFromYear(): ?int
    {
        return $this->productionFromYear;
    }

    public function setProductionFromYear(?int $productionFromYear): self
    {
        $this->productionFromYear = $productionFromYear;

        return $this;
    }

    public function getProductionToYear(): ?int
    {
        return $this->productionToYear;
    }

    public function setProductionToYear(?int $productionToYear): self
    {
        $this->productionToYear = $productionToYear;

        return $this;
    }

    public function getOilCapacityL(): ?string
    {
        return $this->oilCapacityL;
    }

    public function setOilCapacityL(?string $oilCapacityL): self
    {
        $this->oilCapacityL = $oilCapacityL;

        return $this;
    }

    public function getOilCapacityNote(): ?string
    {
        return $this->oilCapacityNote;
    }

    public function setOilCapacityNote(?string $oilCapacityNote): self
    {
        $this->oilCapacityNote = $oilCapacityNote;

        return $this;
    }

    public function getOilViscosity(): ?string
    {
        return $this->oilViscosity;
    }

    public function setOilViscosity(?string $oilViscosity): self
    {
        $this->oilViscosity = $oilViscosity;

        return $this;
    }

    public function getOilSpecification(): ?string
    {
        return $this->oilSpecification;
    }

    public function setOilSpecification(?string $oilSpecification): self
    {
        $this->oilSpecification = $oilSpecification;

        return $this;
    }

    public function getOilIntervalKm(): ?int
    {
        return $this->oilIntervalKm;
    }

    public function setOilIntervalKm(?int $oilIntervalKm): self
    {
        $this->oilIntervalKm = $oilIntervalKm;

        return $this;
    }

    public function getOilIntervalMonths(): ?int
    {
        return $this->oilIntervalMonths;
    }

    public function setOilIntervalMonths(?int $oilIntervalMonths): self
    {
        $this->oilIntervalMonths = $oilIntervalMonths;

        return $this;
    }

    public function getOilDrainPlugTorqueNm(): ?int
    {
        return $this->oilDrainPlugTorqueNm;
    }

    public function setOilDrainPlugTorqueNm(?int $oilDrainPlugTorqueNm): self
    {
        $this->oilDrainPlugTorqueNm = $oilDrainPlugTorqueNm;

        return $this;
    }

    public function getOilFilterTorqueNm(): ?int
    {
        return $this->oilFilterTorqueNm;
    }

    public function setOilFilterTorqueNm(?int $oilFilterTorqueNm): self
    {
        $this->oilFilterTorqueNm = $oilFilterTorqueNm;

        return $this;
    }

    public function getSparkPlugTorqueNm(): ?int
    {
        return $this->sparkPlugTorqueNm;
    }

    public function setSparkPlugTorqueNm(?int $sparkPlugTorqueNm): self
    {
        $this->sparkPlugTorqueNm = $sparkPlugTorqueNm;

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

    public function getConfidence(): ?int
    {
        return $this->confidence;
    }

    public function setConfidence(?int $confidence): self
    {
        $this->confidence = $confidence;

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
