<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use OpenApi\Attributes as OA;

class EngineDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'skoda')]
    private string $manufacturer;

    #[OA\Property(example: 'Octavia')]
    private string $model;

    #[OA\Property(example: 'III', nullable: true)]
    private ?string $generation;

    #[OA\Property(example: 'CHYA', nullable: true)]
    private ?string $engineCode;

    #[OA\Property(example: 'EA211', nullable: true)]
    private ?string $engineFamily;

    #[OA\Property(example: 999, nullable: true)]
    private ?int $displacementCc;

    #[OA\Property(example: 44, nullable: true)]
    private ?int $powerKw;

    #[OA\Property(example: 'petrol', nullable: true)]
    private ?string $fuel;

    #[OA\Property(example: 'EURO 5', nullable: true)]
    private ?string $emissionStandard;

    #[OA\Property(example: 2012, nullable: true)]
    private ?int $productionFromYear;

    #[OA\Property(example: 2019, nullable: true)]
    private ?int $productionToYear;

    #[OA\Property(example: '3.6', nullable: true)]
    private ?string $oilCapacityL;

    #[OA\Property(example: 'with filter', nullable: true)]
    private ?string $oilCapacityNote;

    #[OA\Property(example: '5W-30', nullable: true)]
    private ?string $oilViscosity;

    #[OA\Property(example: 'VW 504.00', nullable: true)]
    private ?string $oilSpecification;

    #[OA\Property(example: 15000, nullable: true)]
    private ?int $oilIntervalKm;

    #[OA\Property(example: 12, nullable: true)]
    private ?int $oilIntervalMonths;

    #[OA\Property(example: 30, nullable: true)]
    private ?int $oilDrainPlugTorqueNm;

    #[OA\Property(example: 20, nullable: true)]
    private ?int $oilFilterTorqueNm;

    #[OA\Property(example: 25, nullable: true)]
    private ?int $sparkPlugTorqueNm;

    #[OA\Property(example: 'MANN', nullable: true)]
    private ?string $source;

    #[OA\Property(example: 4, nullable: true)]
    private ?int $confidence;

    #[OA\Property(nullable: true)]
    private ?string $notes;

    #[OA\Property(example: '2026-02-01T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-02-02T10:00:00+00:00')]
    private string $updatedAt;

    public function __construct(
        string $id,
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
        string $createdAt,
        string $updatedAt,
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
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getGeneration(): ?string
    {
        return $this->generation;
    }

    public function getEngineCode(): ?string
    {
        return $this->engineCode;
    }

    public function getEngineFamily(): ?string
    {
        return $this->engineFamily;
    }

    public function getDisplacementCc(): ?int
    {
        return $this->displacementCc;
    }

    public function getPowerKw(): ?int
    {
        return $this->powerKw;
    }

    public function getFuel(): ?string
    {
        return $this->fuel;
    }

    public function getEmissionStandard(): ?string
    {
        return $this->emissionStandard;
    }

    public function getProductionFromYear(): ?int
    {
        return $this->productionFromYear;
    }

    public function getProductionToYear(): ?int
    {
        return $this->productionToYear;
    }

    public function getOilCapacityL(): ?string
    {
        return $this->oilCapacityL;
    }

    public function getOilCapacityNote(): ?string
    {
        return $this->oilCapacityNote;
    }

    public function getOilViscosity(): ?string
    {
        return $this->oilViscosity;
    }

    public function getOilSpecification(): ?string
    {
        return $this->oilSpecification;
    }

    public function getOilIntervalKm(): ?int
    {
        return $this->oilIntervalKm;
    }

    public function getOilIntervalMonths(): ?int
    {
        return $this->oilIntervalMonths;
    }

    public function getOilDrainPlugTorqueNm(): ?int
    {
        return $this->oilDrainPlugTorqueNm;
    }

    public function getOilFilterTorqueNm(): ?int
    {
        return $this->oilFilterTorqueNm;
    }

    public function getSparkPlugTorqueNm(): ?int
    {
        return $this->sparkPlugTorqueNm;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getConfidence(): ?int
    {
        return $this->confidence;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
