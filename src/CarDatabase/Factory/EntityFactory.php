<?php

declare(strict_types=1);

namespace App\CarDatabase\Factory;

use App\CarDatabase\DBAL\Entity\Engine;
use App\CarDatabase\DBAL\Entity\EngineFilter;
use App\CarDatabase\DBAL\Entity\Filter;
use App\CarDatabase\DBAL\Enum\FilterTypeEnum;
use DateTimeImmutable;
use Symfony\Component\Uid\Factory\UuidFactory;

class EntityFactory
{
    public function __construct(
        private readonly UuidFactory $uuidFactory,
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
        $now = new DateTimeImmutable();

        return new Engine(
            $this->uuidFactory->timeBased()->create(),
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
            $now,
            $now,
        );
    }

    public function createFilter(
        FilterTypeEnum $filterType,
        string $manufacturer,
        string $code,
        ?string $oemCode,
        ?string $thread,
        ?int $heightMm,
        ?int $diameterMm,
        ?string $notes,
    ): Filter {
        $now = new DateTimeImmutable();

        return new Filter(
            $this->uuidFactory->timeBased()->create(),
            $filterType,
            $manufacturer,
            $code,
            $oemCode,
            $thread,
            $heightMm,
            $diameterMm,
            $notes,
            $now,
            $now,
        );
    }

    public function createEngineFilter(
        Engine $engine,
        Filter $filter,
        bool $isPrimary,
        ?string $source,
    ): EngineFilter {
        $now = new DateTimeImmutable();

        return new EngineFilter(
            $this->uuidFactory->timeBased()->create(),
            $engine,
            $filter,
            $isPrimary,
            $source,
            $now,
            $now,
        );
    }
}
