<?php

declare(strict_types=1);

namespace App\CarDatabase\Command;

use App\CarDatabase\EngineService;
use App\CarDatabase\FilterService;
use App\CarDatabase\EngineFilterService;
use App\CarDatabase\DBAL\Enum\FilterTypeEnum;
use App\CarDatabase\DBAL\Repository\EngineRepository;
use App\CarDatabase\DBAL\Repository\FilterRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'car-database:import-engine-data',
    description: 'Import engine and filter data from verified free sources'
)]
class ImportEngineDataCommand extends Command
{
    public function __construct(
        private readonly EngineService $engineService,
        private readonly FilterService $filterService,
        private readonly EngineFilterService $engineFilterService,
        private readonly EngineRepository $engineRepository,
        private readonly FilterRepository $filterRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Importing VW/Skoda Engine and Filter Data');

        // VW 1.4 TSI EA111 engines
        $this->importEA111Engines($io);

        // VW 1.4 TSI EA211 engines
        $this->importEA211Engines($io);

        // VW/Skoda 1.6 TDI EA189 engines
        $this->import16TDIEngines($io);

        // VW/Skoda 2.0 TDI EA189 engines
        $this->import20TDIEngines($io);

        $io->success('Engine and filter data import completed!');
        return Command::SUCCESS;
    }

    private function importEA111Engines(SymfonyStyle $io): void
    {
        $io->section('Importing EA111 1.4 TSI engines');

        $ea111Engines = [
            [
                'manufacturer' => 'VW',
                'model' => 'Golf V, Golf VI, Jetta, Passat',
                'generation' => 'Mk5, Mk6, B6',
                'engine_code' => 'CAXA',
                'engine_family' => 'EA111',
                'displacement_cc' => 1390,
                'power_kw' => 90,
                'fuel' => 'petrol',
                'emission_standard' => 'Euro 5',
                'production_from_year' => 2008,
                'production_to_year' => 2014,
                'oil_capacity_l' => '3.8',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30, 5W-40',
                'oil_specification' => 'VW 502.00/505.00',
                'oil_interval_km' => 15000,
                'oil_interval_months' => 12,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => null,
                'spark_plug_torque_nm' => 25,
                'source' => 'motorreviewer.com EA111 specs, VW forums',
                'confidence' => 4,
                'notes' => '122hp variant, timing chain issues common',
            ],
            [
                'manufacturer' => 'Skoda',
                'model' => 'Octavia II, Superb II',
                'generation' => 'Mk2',
                'engine_code' => 'CAXA',
                'engine_family' => 'EA111',
                'displacement_cc' => 1390,
                'power_kw' => 90,
                'fuel' => 'petrol',
                'emission_standard' => 'Euro 5',
                'production_from_year' => 2008,
                'production_to_year' => 2013,
                'oil_capacity_l' => '3.8',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30, 5W-40',
                'oil_specification' => 'VW 502.00/505.00',
                'oil_interval_km' => 15000,
                'oil_interval_months' => 12,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => null,
                'spark_plug_torque_nm' => 25,
                'source' => 'motorreviewer.com EA111 specs, Skoda forums',
                'confidence' => 4,
                'notes' => '122hp variant',
            ],
            [
                'manufacturer' => 'VW',
                'model' => 'Golf V, Golf VI, Tiguan',
                'generation' => 'Mk5, Mk6',
                'engine_code' => 'CAXC',
                'engine_family' => 'EA111',
                'displacement_cc' => 1390,
                'power_kw' => 92,
                'fuel' => 'petrol',
                'emission_standard' => 'Euro 5',
                'production_from_year' => 2007,
                'production_to_year' => 2014,
                'oil_capacity_l' => '3.8',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30, 5W-40',
                'oil_specification' => 'VW 502.00/505.00',
                'oil_interval_km' => 15000,
                'oil_interval_months' => 12,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => null,
                'spark_plug_torque_nm' => 25,
                'source' => 'motorreviewer.com EA111 specs',
                'confidence' => 4,
                'notes' => '125hp variant',
            ],
        ];

        foreach ($ea111Engines as $engineData) {
            // Check if engine already exists
            $existingEngine = $this->engineRepository->findOneBy([
                'manufacturer' => $engineData['manufacturer'],
                'engineCode' => $engineData['engine_code'],
                'model' => $engineData['model'],
            ]);

            if ($existingEngine) {
                $io->warning(sprintf(
                    'Engine %s %s (%s) already exists, skipping',
                    $engineData['manufacturer'],
                    $engineData['engine_code'],
                    $engineData['model']
                ));
                continue;
            }

            $engine = $this->engineService->createEngine(
                manufacturer: $engineData['manufacturer'],
                model: $engineData['model'],
                generation: $engineData['generation'],
                engineCode: $engineData['engine_code'],
                engineFamily: $engineData['engine_family'],
                displacementCc: $engineData['displacement_cc'],
                powerKw: $engineData['power_kw'],
                fuel: $engineData['fuel'],
                emissionStandard: $engineData['emission_standard'],
                productionFromYear: $engineData['production_from_year'],
                productionToYear: $engineData['production_to_year'],
                oilCapacityL: $engineData['oil_capacity_l'],
                oilCapacityNote: $engineData['oil_capacity_note'],
                oilViscosity: $engineData['oil_viscosity'],
                oilSpecification: $engineData['oil_specification'],
                oilIntervalKm: $engineData['oil_interval_km'],
                oilIntervalMonths: $engineData['oil_interval_months'],
                oilDrainPlugTorqueNm: $engineData['oil_drain_plug_torque_nm'],
                oilFilterTorqueNm: $engineData['oil_filter_torque_nm'],
                sparkPlugTorqueNm: $engineData['spark_plug_torque_nm'],
                source: $engineData['source'],
                confidence: $engineData['confidence'],
                notes: $engineData['notes'],
            );

            // Add oil filters for EA111 engines
            $this->addOilFiltersForEngine($engine, 'W712/94', 'MANN', '074115562', $io);
            $this->addOilFiltersForEngine($engine, 'HU719/7X', 'MANN', '06A115561B', $io);

            $io->success(sprintf(
                'Imported engine: %s %s (%s) - %s kW',
                $engineData['manufacturer'],
                $engineData['engine_code'],
                $engineData['model'],
                $engineData['power_kw']
            ));
        }
    }

    private function importEA211Engines(SymfonyStyle $io): void
    {
        $io->section('Importing EA211 1.4 TSI engines');

        $ea211Engines = [
            [
                'manufacturer' => 'VW',
                'model' => 'Golf VII, Passat B8, Tiguan',
                'generation' => 'Mk7, B8',
                'engine_code' => 'CZCA',
                'engine_family' => 'EA211',
                'displacement_cc' => 1395,
                'power_kw' => 92,
                'fuel' => 'petrol',
                'emission_standard' => 'Euro 6',
                'production_from_year' => 2012,
                'production_to_year' => null,
                'oil_capacity_l' => '3.8',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30, 5W-40',
                'oil_specification' => 'VW 502.00/504.00',
                'oil_interval_km' => 15000,
                'oil_interval_months' => 12,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => 25,
                'spark_plug_torque_nm' => 25,
                'source' => 'motorreviewer.com EA211 specs',
                'confidence' => 4,
                'notes' => '125hp variant, improved reliability over EA111',
            ],
            [
                'manufacturer' => 'Skoda',
                'model' => 'Octavia III, Superb III',
                'generation' => 'Mk3',
                'engine_code' => 'CZCA',
                'engine_family' => 'EA211',
                'displacement_cc' => 1395,
                'power_kw' => 92,
                'fuel' => 'petrol',
                'emission_standard' => 'Euro 6',
                'production_from_year' => 2013,
                'production_to_year' => null,
                'oil_capacity_l' => '3.8',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30, 5W-40',
                'oil_specification' => 'VW 502.00/504.00',
                'oil_interval_km' => 15000,
                'oil_interval_months' => 12,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => 25,
                'spark_plug_torque_nm' => 25,
                'source' => 'motorreviewer.com EA211 specs',
                'confidence' => 4,
                'notes' => '125hp variant',
            ],
        ];

        foreach ($ea211Engines as $engineData) {
            $existingEngine = $this->engineRepository->findOneBy([
                'manufacturer' => $engineData['manufacturer'],
                'engineCode' => $engineData['engine_code'],
                'model' => $engineData['model'],
            ]);

            if ($existingEngine) {
                $io->warning(sprintf(
                    'Engine %s %s (%s) already exists, skipping',
                    $engineData['manufacturer'],
                    $engineData['engine_code'],
                    $engineData['model']
                ));
                continue;
            }

            $engine = $this->engineService->createEngine(
                manufacturer: $engineData['manufacturer'],
                model: $engineData['model'],
                generation: $engineData['generation'],
                engineCode: $engineData['engine_code'],
                engineFamily: $engineData['engine_family'],
                displacementCc: $engineData['displacement_cc'],
                powerKw: $engineData['power_kw'],
                fuel: $engineData['fuel'],
                emissionStandard: $engineData['emission_standard'],
                productionFromYear: $engineData['production_from_year'],
                productionToYear: $engineData['production_to_year'],
                oilCapacityL: $engineData['oil_capacity_l'],
                oilCapacityNote: $engineData['oil_capacity_note'],
                oilViscosity: $engineData['oil_viscosity'],
                oilSpecification: $engineData['oil_specification'],
                oilIntervalKm: $engineData['oil_interval_km'],
                oilIntervalMonths: $engineData['oil_interval_months'],
                oilDrainPlugTorqueNm: $engineData['oil_drain_plug_torque_nm'],
                oilFilterTorqueNm: $engineData['oil_filter_torque_nm'],
                sparkPlugTorqueNm: $engineData['spark_plug_torque_nm'],
                source: $engineData['source'],
                confidence: $engineData['confidence'],
                notes: $engineData['notes'],
            );

            // Add oil filters for EA211 engines
            $this->addOilFiltersForEngine($engine, 'HU6013Z', 'MANN', '06L115562', $io);

            $io->success(sprintf(
                'Imported engine: %s %s (%s) - %s kW',
                $engineData['manufacturer'],
                $engineData['engine_code'],
                $engineData['model'],
                $engineData['power_kw']
            ));
        }
    }

    private function addOilFiltersForEngine(
        $engine,
        string $filterCode,
        string $manufacturer,
        string $oemCode,
        SymfonyStyle $io
    ): void {
        // Check if filter already exists
        $filter = $this->filterRepository->findOneBy([
            'manufacturer' => $manufacturer,
            'code' => $filterCode,
        ]);

        if (!$filter) {
            $filter = $this->filterService->createFilter(
                filterType: FilterTypeEnum::OIL,
                manufacturer: $manufacturer,
                code: $filterCode,
                oemCode: $oemCode,
                thread: null,
                heightMm: null,
                diameterMm: null,
                notes: 'MANN-FILTER online catalog'
            );

            $io->info(sprintf('Created filter: %s %s', $manufacturer, $filterCode));
        }

        // Link engine to filter
        $this->engineFilterService->createEngineFilter(
            engine: $engine,
            filter: $filter,
            isPrimary: true,
            source: 'MANN-FILTER online catalog'
        );
    }

    private function import16TDIEngines(SymfonyStyle $io): void
    {
        $io->section('Importing EA189 1.6 TDI engines');

        $tdi16Engines = [
            [
                'manufacturer' => 'VW',
                'model' => 'Golf VI, Passat B7, Touran',
                'generation' => 'Mk6, B7',
                'engine_code' => 'CAYC',
                'engine_family' => 'EA189',
                'displacement_cc' => 1598,
                'power_kw' => 77,
                'fuel' => 'diesel',
                'emission_standard' => 'Euro 5',
                'production_from_year' => 2009,
                'production_to_year' => 2015,
                'oil_capacity_l' => '4.3',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30, 0W-30',
                'oil_specification' => 'VW 507.00',
                'oil_interval_km' => 30000,
                'oil_interval_months' => 24,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => 25,
                'spark_plug_torque_nm' => null,
                'source' => 'motorreviewer.com EA189 specs, VW service data',
                'confidence' => 4,
                'notes' => '105hp variant, DPF equipped',
            ],
            [
                'manufacturer' => 'Skoda',
                'model' => 'Octavia II, Superb II, Yeti',
                'generation' => 'Mk2',
                'engine_code' => 'CAYC',
                'engine_family' => 'EA189',
                'displacement_cc' => 1598,
                'power_kw' => 77,
                'fuel' => 'diesel',
                'emission_standard' => 'Euro 5',
                'production_from_year' => 2009,
                'production_to_year' => 2015,
                'oil_capacity_l' => '4.3',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30, 0W-30',
                'oil_specification' => 'VW 507.00',
                'oil_interval_km' => 30000,
                'oil_interval_months' => 24,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => 25,
                'spark_plug_torque_nm' => null,
                'source' => 'motorreviewer.com EA189 specs, Skoda service data',
                'confidence' => 4,
                'notes' => '105hp variant, DPF equipped',
            ],
            [
                'manufacturer' => 'Skoda',
                'model' => 'Octavia III, Rapid',
                'generation' => 'Mk3',
                'engine_code' => 'CLHA',
                'engine_family' => 'EA189',
                'displacement_cc' => 1598,
                'power_kw' => 77,
                'fuel' => 'diesel',
                'emission_standard' => 'Euro 5',
                'production_from_year' => 2012,
                'production_to_year' => 2015,
                'oil_capacity_l' => '4.3',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30, 0W-30',
                'oil_specification' => 'VW 507.00',
                'oil_interval_km' => 30000,
                'oil_interval_months' => 24,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => 25,
                'spark_plug_torque_nm' => null,
                'source' => 'motorreviewer.com EA189 specs, Skoda service data',
                'confidence' => 4,
                'notes' => '105hp variant, DPF equipped',
            ],
        ];

        foreach ($tdi16Engines as $engineData) {
            $existingEngine = $this->engineRepository->findOneBy([
                'manufacturer' => $engineData['manufacturer'],
                'engineCode' => $engineData['engine_code'],
                'model' => $engineData['model'],
            ]);

            if ($existingEngine) {
                $io->warning(sprintf(
                    'Engine %s %s (%s) already exists, skipping',
                    $engineData['manufacturer'],
                    $engineData['engine_code'],
                    $engineData['model']
                ));
                continue;
            }

            $engine = $this->engineService->createEngine(
                manufacturer: $engineData['manufacturer'],
                model: $engineData['model'],
                generation: $engineData['generation'],
                engineCode: $engineData['engine_code'],
                engineFamily: $engineData['engine_family'],
                displacementCc: $engineData['displacement_cc'],
                powerKw: $engineData['power_kw'],
                fuel: $engineData['fuel'],
                emissionStandard: $engineData['emission_standard'],
                productionFromYear: $engineData['production_from_year'],
                productionToYear: $engineData['production_to_year'],
                oilCapacityL: $engineData['oil_capacity_l'],
                oilCapacityNote: $engineData['oil_capacity_note'],
                oilViscosity: $engineData['oil_viscosity'],
                oilSpecification: $engineData['oil_specification'],
                oilIntervalKm: $engineData['oil_interval_km'],
                oilIntervalMonths: $engineData['oil_interval_months'],
                oilDrainPlugTorqueNm: $engineData['oil_drain_plug_torque_nm'],
                oilFilterTorqueNm: $engineData['oil_filter_torque_nm'],
                sparkPlugTorqueNm: $engineData['spark_plug_torque_nm'],
                source: $engineData['source'],
                confidence: $engineData['confidence'],
                notes: $engineData['notes'],
            );

            // Add oil filters for 1.6 TDI engines
            $this->addOilFiltersForEngine($engine, 'HU7008Z', 'MANN', '03L115562', $io);

            $io->success(sprintf(
                'Imported engine: %s %s (%s) - %s kW',
                $engineData['manufacturer'],
                $engineData['engine_code'],
                $engineData['model'],
                $engineData['power_kw']
            ));
        }
    }

    private function import20TDIEngines(SymfonyStyle $io): void
    {
        $io->section('Importing EA189 2.0 TDI engines');

        $tdi20Engines = [
            [
                'manufacturer' => 'VW',
                'model' => 'Golf VI, Passat B7, Tiguan',
                'generation' => 'Mk6, B7',
                'engine_code' => 'CFHC',
                'engine_family' => 'EA189',
                'displacement_cc' => 1968,
                'power_kw' => 103,
                'fuel' => 'diesel',
                'emission_standard' => 'Euro 5',
                'production_from_year' => 2010,
                'production_to_year' => 2015,
                'oil_capacity_l' => '4.3',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30',
                'oil_specification' => 'VW 507.00',
                'oil_interval_km' => 30000,
                'oil_interval_months' => 24,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => 25,
                'spark_plug_torque_nm' => null,
                'source' => 'motorreviewer.com EA189 specs',
                'confidence' => 4,
                'notes' => '140hp variant, DPF equipped, common rail',
            ],
            [
                'manufacturer' => 'Skoda',
                'model' => 'Octavia II, Superb II',
                'generation' => 'Mk2',
                'engine_code' => 'CFHC',
                'engine_family' => 'EA189',
                'displacement_cc' => 1968,
                'power_kw' => 103,
                'fuel' => 'diesel',
                'emission_standard' => 'Euro 5',
                'production_from_year' => 2010,
                'production_to_year' => 2015,
                'oil_capacity_l' => '4.3',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30',
                'oil_specification' => 'VW 507.00',
                'oil_interval_km' => 30000,
                'oil_interval_months' => 24,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => 25,
                'spark_plug_torque_nm' => null,
                'source' => 'motorreviewer.com EA189 specs',
                'confidence' => 4,
                'notes' => '140hp variant, DPF equipped, common rail',
            ],
            [
                'manufacturer' => 'VW',
                'model' => 'Golf VI, Passat B7, Tiguan',
                'generation' => 'Mk6, B7',
                'engine_code' => 'CFFB',
                'engine_family' => 'EA189',
                'displacement_cc' => 1968,
                'power_kw' => 103,
                'fuel' => 'diesel',
                'emission_standard' => 'Euro 5',
                'production_from_year' => 2009,
                'production_to_year' => 2014,
                'oil_capacity_l' => '4.3',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30',
                'oil_specification' => 'VW 507.00',
                'oil_interval_km' => 30000,
                'oil_interval_months' => 24,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => 25,
                'spark_plug_torque_nm' => null,
                'source' => 'motorreviewer.com EA189 specs',
                'confidence' => 4,
                'notes' => '140hp variant, DPF equipped',
            ],
            [
                'manufacturer' => 'VW',
                'model' => 'Golf VI, Passat CC, Tiguan',
                'generation' => 'Mk6',
                'engine_code' => 'CFGB',
                'engine_family' => 'EA189',
                'displacement_cc' => 1968,
                'power_kw' => 125,
                'fuel' => 'diesel',
                'emission_standard' => 'Euro 5',
                'production_from_year' => 2008,
                'production_to_year' => 2013,
                'oil_capacity_l' => '4.3',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30',
                'oil_specification' => 'VW 507.00',
                'oil_interval_km' => 30000,
                'oil_interval_months' => 24,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => 25,
                'spark_plug_torque_nm' => null,
                'source' => 'motorreviewer.com EA189 specs',
                'confidence' => 4,
                'notes' => '170hp variant, DPF equipped, variable turbo',
            ],
            [
                'manufacturer' => 'Skoda',
                'model' => 'Octavia II, Superb II',
                'generation' => 'Mk2',
                'engine_code' => 'CEGA',
                'engine_family' => 'EA189',
                'displacement_cc' => 1968,
                'power_kw' => 125,
                'fuel' => 'diesel',
                'emission_standard' => 'Euro 5',
                'production_from_year' => 2009,
                'production_to_year' => 2013,
                'oil_capacity_l' => '4.3',
                'oil_capacity_note' => 'with filter',
                'oil_viscosity' => '5W-30',
                'oil_specification' => 'VW 507.00',
                'oil_interval_km' => 30000,
                'oil_interval_months' => 24,
                'oil_drain_plug_torque_nm' => 30,
                'oil_filter_torque_nm' => 25,
                'spark_plug_torque_nm' => null,
                'source' => 'motorreviewer.com EA189 specs',
                'confidence' => 4,
                'notes' => '170hp variant, DPF equipped',
            ],
        ];

        foreach ($tdi20Engines as $engineData) {
            $existingEngine = $this->engineRepository->findOneBy([
                'manufacturer' => $engineData['manufacturer'],
                'engineCode' => $engineData['engine_code'],
                'model' => $engineData['model'],
            ]);

            if ($existingEngine) {
                $io->warning(sprintf(
                    'Engine %s %s (%s) already exists, skipping',
                    $engineData['manufacturer'],
                    $engineData['engine_code'],
                    $engineData['model']
                ));
                continue;
            }

            $engine = $this->engineService->createEngine(
                manufacturer: $engineData['manufacturer'],
                model: $engineData['model'],
                generation: $engineData['generation'],
                engineCode: $engineData['engine_code'],
                engineFamily: $engineData['engine_family'],
                displacementCc: $engineData['displacement_cc'],
                powerKw: $engineData['power_kw'],
                fuel: $engineData['fuel'],
                emissionStandard: $engineData['emission_standard'],
                productionFromYear: $engineData['production_from_year'],
                productionToYear: $engineData['production_to_year'],
                oilCapacityL: $engineData['oil_capacity_l'],
                oilCapacityNote: $engineData['oil_capacity_note'],
                oilViscosity: $engineData['oil_viscosity'],
                oilSpecification: $engineData['oil_specification'],
                oilIntervalKm: $engineData['oil_interval_km'],
                oilIntervalMonths: $engineData['oil_interval_months'],
                oilDrainPlugTorqueNm: $engineData['oil_drain_plug_torque_nm'],
                oilFilterTorqueNm: $engineData['oil_filter_torque_nm'],
                sparkPlugTorqueNm: $engineData['spark_plug_torque_nm'],
                source: $engineData['source'],
                confidence: $engineData['confidence'],
                notes: $engineData['notes'],
            );

            // Add oil filters for 2.0 TDI engines
            $this->addOilFiltersForEngine($engine, 'HU7008Z', 'MANN', '03L115562', $io);

            $io->success(sprintf(
                'Imported engine: %s %s (%s) - %s kW',
                $engineData['manufacturer'],
                $engineData['engine_code'],
                $engineData['model'],
                $engineData['power_kw']
            ));
        }
    }
}
