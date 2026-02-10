<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class EngineUpdateRequestDTO
{
    #[OA\Property(example: 'skoda')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 120)]
    private string $manufacturer;

    #[OA\Property(example: 'Octavia')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $model;

    #[OA\Property(example: 'III', nullable: true)]
    #[Assert\Length(max: 64)]
    private ?string $generation = null;

    #[OA\Property(example: 'CHYA', nullable: true)]
    #[Assert\Length(max: 64)]
    private ?string $engineCode = null;

    #[OA\Property(example: 'EA211', nullable: true)]
    #[Assert\Length(max: 64)]
    private ?string $engineFamily = null;

    #[OA\Property(example: 999, nullable: true)]
    #[Assert\Positive]
    private ?int $displacementCc = null;

    #[OA\Property(example: 44, nullable: true)]
    #[Assert\Positive]
    private ?int $powerKw = null;

    #[OA\Property(example: 'petrol', nullable: true)]
    #[Assert\Length(max: 32)]
    private ?string $fuel = null;

    #[OA\Property(example: 'EURO 5', nullable: true)]
    #[Assert\Length(max: 32)]
    private ?string $emissionStandard = null;

    #[OA\Property(example: 2012, nullable: true)]
    #[Assert\Positive]
    private ?int $productionFromYear = null;

    #[OA\Property(example: 2019, nullable: true)]
    #[Assert\Positive]
    private ?int $productionToYear = null;

    #[OA\Property(example: '3.6', nullable: true)]
    private ?string $oilCapacityL = null;

    #[OA\Property(example: 'with filter', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $oilCapacityNote = null;

    #[OA\Property(example: '5W-30', nullable: true)]
    #[Assert\Length(max: 32)]
    private ?string $oilViscosity = null;

    #[OA\Property(example: 'VW 504.00', nullable: true)]
    #[Assert\Length(max: 128)]
    private ?string $oilSpecification = null;

    #[OA\Property(example: 15000, nullable: true)]
    #[Assert\Positive]
    private ?int $oilIntervalKm = null;

    #[OA\Property(example: 12, nullable: true)]
    #[Assert\Positive]
    private ?int $oilIntervalMonths = null;

    #[OA\Property(example: 30, nullable: true)]
    #[Assert\Positive]
    private ?int $oilDrainPlugTorqueNm = null;

    #[OA\Property(example: 20, nullable: true)]
    #[Assert\Positive]
    private ?int $oilFilterTorqueNm = null;

    #[OA\Property(example: 25, nullable: true)]
    #[Assert\Positive]
    private ?int $sparkPlugTorqueNm = null;

    #[OA\Property(example: 'MANN', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $source = null;

    #[OA\Property(example: 4, nullable: true)]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $confidence = null;

    #[OA\Property(nullable: true)]
    private ?string $notes = null;

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
}
