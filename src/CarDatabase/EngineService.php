<?php

declare(strict_types=1);

namespace App\CarDatabase;

use App\CarDatabase\DBAL\Entity\Engine;
use App\CarDatabase\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class EngineService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createEngine(
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
    ): Engine {
        $engine = $this->entityFactory->createEngine(
            $manufacturer,
            $model,
            $generation,
            $engineCode,
            $engineFamily,
            $displacementCc,
            $powerKw,
            $fuel,
            $emissionStandard,
            $productionFromYear,
            $productionToYear,
            $oilCapacityL,
            $oilCapacityNote,
            $oilViscosity,
            $oilSpecification,
            $oilIntervalKm,
            $oilIntervalMonths,
            $oilDrainPlugTorqueNm,
            $oilFilterTorqueNm,
            $sparkPlugTorqueNm,
            $source,
            $confidence,
            $notes,
        );

        $this->entityManager->persist($engine);
        $this->entityManager->flush();

        return $engine;
    }

    public function updateEngine(
        Engine $engine,
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
    ): Engine {
        $engine->setManufacturer($manufacturer);
        $engine->setModel($model);
        $engine->setGeneration($generation);
        $engine->setEngineCode($engineCode);
        $engine->setEngineFamily($engineFamily);
        $engine->setDisplacementCc($displacementCc);
        $engine->setPowerKw($powerKw);
        $engine->setFuel($fuel);
        $engine->setEmissionStandard($emissionStandard);
        $engine->setProductionFromYear($productionFromYear);
        $engine->setProductionToYear($productionToYear);
        $engine->setOilCapacityL($oilCapacityL);
        $engine->setOilCapacityNote($oilCapacityNote);
        $engine->setOilViscosity($oilViscosity);
        $engine->setOilSpecification($oilSpecification);
        $engine->setOilIntervalKm($oilIntervalKm);
        $engine->setOilIntervalMonths($oilIntervalMonths);
        $engine->setOilDrainPlugTorqueNm($oilDrainPlugTorqueNm);
        $engine->setOilFilterTorqueNm($oilFilterTorqueNm);
        $engine->setSparkPlugTorqueNm($sparkPlugTorqueNm);
        $engine->setSource($source);
        $engine->setConfidence($confidence);
        $engine->setNotes($notes);
        $engine->setUpdatedAt(new DateTimeImmutable());

        $this->entityManager->flush();

        return $engine;
    }

    public function deleteEngine(Engine $engine): void
    {
        $this->entityManager->remove($engine);
        $this->entityManager->flush();
    }
}
