<?php

declare(strict_types=1);

namespace App\OilService;

use App\CarDatabase\DBAL\Entity\Engine;
use App\CarDatabase\DBAL\Enum\CustomerCarBrandEnum;
use App\CarDatabase\DBAL\Repository\EngineRepository;
use App\Domain\Error\ErrorCollection;
use App\Domain\Error\ErrorItem;
use App\Domain\Exception\ValidationException;
use App\OilService\DBAL\Entity\CustomerCar;
use App\OilService\DBAL\Entity\CustomerCarHistory;
use App\OilService\DBAL\Entity\User;
use App\OilService\Factory\EntityFactory;
use App\VehicleDataCube\VehicleDataCubeService;
use Doctrine\ORM\EntityManagerInterface;

class CustomerCarService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly VehicleDataCubeService $vehicleDataCubeService,
        private readonly EngineRepository $engineRepository,
    ) {
    }

    public function createCustomerCar(
        string $licensePlate,
        ?CustomerCarBrandEnum $brand,
        ?string $model,
        ?string $vin,
        ?User $user,
        ?Engine $engine = null,
    ): CustomerCar {
        $car = $this->entityFactory->createCustomerCar(
            $licensePlate,
            $brand,
            $model,
            $vin,
            $user,
            $engine,
        );

        if ($user !== null) {
            $this->addHistory($car, $user);
        }

        $this->entityManager->persist($car);
        $this->entityManager->flush();

        return $car;
    }

    public function updateCustomerCar(
        CustomerCar $car,
        string $licensePlate,
        ?CustomerCarBrandEnum $brand,
        ?string $model,
        ?string $vin,
        ?User $user,
        ?Engine $engine,
    ): CustomerCar {
        $car->setLicensePlate($licensePlate);
        $car->setBrand($brand);
        $car->setModel($model);
        $car->setVin($vin);
        $car->setEngine($engine);

        $this->assignUser($car, $user);

        $this->entityManager->flush();

        return $car;
    }

    public function assignUser(CustomerCar $car, ?User $user): void
    {
        $currentUser = $car->getUser();

        if ($currentUser?->getId()->__toString() === $user?->getId()->__toString()) {
            return;
        }

        $car->setUser($user);

        if ($user !== null) {
            $this->addHistory($car, $user);
        }
    }

    public function deleteCustomerCar(CustomerCar $car): void
    {
        if ($car->getUser() !== null) {
            throw $this->createCarAssignedException('Car is assigned to a customer. Remove customer assignment first.');
        }

        if ($car->getOrders()->count() > 0) {
            throw $this->createCarAssignedException('Car is assigned to orders. Remove it from orders first.');
        }

        if ($car->getHistory()->count() > 0) {
            throw $this->createCarAssignedException('Car has history records. Remove history first.');
        }

        $this->entityManager->remove($car);
        $this->entityManager->flush();
    }

    public function deleteCustomerCarHistory(CustomerCar $car): void
    {
        foreach ($car->getHistory()->toArray() as $history) {
            $this->entityManager->remove($history);
        }

        $this->entityManager->flush();
    }

    public function updateFromDataCube(CustomerCar $car, string $vin): bool
    {
        $data = $this->vehicleDataCubeService->fetchVehicleDataByVin($vin);

        if ($data === null) {
            return false;
        }

        $car->applyDataCubeData($data);

        $brand = $this->resolveBrandFromDataCube($data['TovarniZnacka'] ?? null);
        if ($brand !== null) {
            $car->setBrand($brand);
        }

        $model = $this->resolveModelFromDataCube($data);
        if ($model !== null) {
            $car->setModel($model);
        }

        if ($car->getVin() === null && is_string($data['VIN'] ?? null)) {
            $car->setVin($data['VIN']);
        }

        $this->tryAssignEngineFromCarData($car);

        $this->entityManager->flush();

        return true;
    }

    public function resolveEngineByCode(CustomerCar $car, string $engineCode): ?Engine
    {
        $engineCodeValue = $this->normalizeEngineCode($engineCode);

        if ($engineCodeValue === '') {
            return null;
        }

        $engineCodeCandidates = $this->buildEngineCodeCandidates($engineCodeValue);
        $manufacturer = $this->resolveManufacturerForMatching($car);

        if ($manufacturer !== null) {
            foreach ($engineCodeCandidates as $candidate) {
                $engine = $this->engineRepository->findOneByEngineCodeAndManufacturerCaseInsensitive($candidate, $manufacturer);

                if ($engine !== null) {
                    return $this->assignEngine($car, $engine);
                }
            }
        }

        foreach ($engineCodeCandidates as $candidate) {
            $engine = $this->engineRepository->findOneByEngineCode($candidate);

            if ($engine !== null) {
                return $this->assignEngine($car, $engine);
            }
        }

        $engine = $this->resolveEngineByCarData($car);

        if ($engine !== null) {
            return $this->assignEngine($car, $engine);
        }

        return null;
    }

    public function tryAssignEngineFromCarData(CustomerCar $car): void
    {
        $engineCode = $car->getDkMotorTyp();

        if (!is_string($engineCode) || trim($engineCode) === '') {
            return;
        }

        $this->resolveEngineByCode($car, $engineCode);
    }

    private function resolveBrandFromDataCube(mixed $brandName): ?CustomerCarBrandEnum
    {
        if (!is_string($brandName) || trim($brandName) === '') {
            return null;
        }

        $normalized = $this->normalizeBrand($brandName);

        foreach (CustomerCarBrandEnum::cases() as $case) {
            $caseNormalized = $this->normalizeBrand(str_replace('_', ' ', $case->value));

            if ($normalized === $caseNormalized) {
                return $case;
            }
        }

        return CustomerCarBrandEnum::UNASSIGNED;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function resolveModelFromDataCube(array $data): ?string
    {
        $model = $data['ObchodniOznaceni'] ?? null;
        if (is_string($model) && trim($model) !== '') {
            return $model;
        }

        $type = $data['Typ'] ?? null;
        if (is_string($type) && trim($type) !== '') {
            return $type;
        }

        return null;
    }

    private function addHistory(CustomerCar $car, User $user): CustomerCarHistory
    {
        $history = $this->entityFactory->createCustomerCarHistory($car, $user);
        $car->addHistory($history);
        $this->entityManager->persist($history);

        return $history;
    }

    private function normalizeBrand(string $value): string
    {
        $normalized = trim($value);
        $normalized = mb_strtoupper($normalized);
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT', $normalized) ?: $normalized;
        $normalized = preg_replace('/[^A-Z0-9]+/', '', $normalized) ?? $normalized;

        return $normalized;
    }

    private function normalizeComparableText(string $value): string
    {
        $normalized = trim($value);
        $normalized = mb_strtoupper($normalized);
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT', $normalized) ?: $normalized;
        $normalized = preg_replace('/[^A-Z0-9]+/', '', $normalized) ?? $normalized;

        return $normalized;
    }

    private function normalizeEngineCode(string $value): string
    {
        $normalized = trim($value);
        $normalized = mb_strtoupper($normalized);

        return $normalized;
    }

    /**
     * @return string[]
     */
    private function buildEngineCodeCandidates(string $engineCode): array
    {
        $candidates = [$engineCode];
        $normalized = preg_replace('/[^A-Z0-9]+/', '', $engineCode) ?? $engineCode;

        if ($normalized !== '' && $normalized !== $engineCode) {
            $candidates[] = $normalized;
        }

        return array_values(array_unique($candidates));
    }

    private function resolveManufacturerForMatching(CustomerCar $car): ?string
    {
        $brand = $car->getBrand()?->value;
        if (is_string($brand) && trim($brand) !== '') {
            return $brand;
        }

        $dataCubeBrand = $car->getDkTovarniZnacka();
        if (is_string($dataCubeBrand) && trim($dataCubeBrand) !== '') {
            return $dataCubeBrand;
        }

        $engineManufacturer = $car->getDkMotorVyrobce();
        if (is_string($engineManufacturer) && trim($engineManufacturer) !== '') {
            return $engineManufacturer;
        }

        return null;
    }

    private function resolveEngineByCarData(CustomerCar $car): ?Engine
    {
        $manufacturer = $this->resolveManufacturerForMatching($car);
        $displacementCc = $this->parseIntFromString($car->getDkMotorZdvihObjem());
        $powerKw = $this->parsePowerKw($car->getDkMotorMaxVykon());
        $fuel = $this->normalizeFuel($car->getDkPalivo());

        $candidates = $this->engineRepository->findMatchingCandidates(
            $manufacturer,
            $displacementCc,
            $powerKw,
            $fuel,
        );

        if (count($candidates) === 1) {
            return $candidates[0];
        }

        if ($candidates === []) {
            return null;
        }

        $modelTokens = $this->buildModelTokens($car);

        $best = null;
        $bestScore = 0;
        $isTie = false;

        foreach ($candidates as $candidate) {
            $score = 0;

            if ($manufacturer !== null && strcasecmp($candidate->getManufacturer(), $manufacturer) === 0) {
                $score += 2;
            }

            if ($displacementCc !== null && $candidate->getDisplacementCc() === $displacementCc) {
                $score += 2;
            }

            if ($powerKw !== null && $candidate->getPowerKw() !== null && abs($candidate->getPowerKw() - $powerKw) <= 5) {
                $score += 2;
            }

            if ($fuel !== null && $candidate->getFuel() === $fuel) {
                $score += 1;
            }

            if ($modelTokens !== []) {
                $modelNormalized = $this->normalizeComparableText($candidate->getModel());
                foreach ($modelTokens as $token) {
                    if (str_contains($modelNormalized, $token)) {
                        $score += 1;
                    }
                }
            }

            if ($score > $bestScore) {
                $best = $candidate;
                $bestScore = $score;
                $isTie = false;
            } elseif ($score === $bestScore && $score > 0) {
                $isTie = true;
            }
        }

        if ($best !== null && $bestScore >= 5 && !$isTie) {
            return $best;
        }

        return null;
    }

    /**
     * @return string[]
     */
    private function buildModelTokens(CustomerCar $car): array
    {
        $tokens = [];

        foreach ([$car->getModel(), $car->getDkObchodniOznaceni(), $car->getDkTyp()] as $value) {
            foreach ($this->extractModelTokens($value) as $token) {
                $tokens[] = $token;
            }
        }

        return array_values(array_unique($tokens));
    }

    /**
     * @return string[]
     */
    private function extractModelTokens(?string $value): array
    {
        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $normalized = mb_strtoupper($value);
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT', $normalized) ?: $normalized;

        $parts = preg_split('/[^A-Z0-9]+/', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_filter(
            $parts,
            static fn (string $part): bool => strlen($part) >= 3
        ));
    }

    private function parseIntFromString(?string $value): ?int
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        if (preg_match('/(\d{2,})/', $value, $matches) !== 1) {
            return null;
        }

        return (int) $matches[1];
    }

    private function parsePowerKw(?string $value): ?int
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        if (preg_match('/(\d{2,})\s*(?:kW|\/|$)/i', $value, $matches) === 1) {
            return (int) $matches[1];
        }

        if (preg_match('/(\d{2,})/', $value, $matches) === 1) {
            return (int) $matches[1];
        }

        return null;
    }

    private function normalizeFuel(?string $value): ?string
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        $normalized = mb_strtoupper(trim($value));

        if (str_contains($normalized, 'NAFTA') || str_contains($normalized, 'DIESEL')) {
            return 'diesel';
        }

        if (str_contains($normalized, 'BA') || str_contains($normalized, 'BENZ') || str_contains($normalized, 'PETROL') || str_contains($normalized, 'GASOLINE')) {
            return 'petrol';
        }

        return null;
    }

    private function assignEngine(CustomerCar $car, Engine $engine): Engine
    {
        $car->setEngine($engine);
        $this->entityManager->flush();

        return $engine;
    }

    private function createCarAssignedException(string $message): ValidationException
    {
        $errorCollection = new ErrorCollection();
        $errorCollection->add(
            new ErrorItem(
                $message,
                'customerCarAssigned',
                null,
            )
        );

        return new ValidationException(errorCollection: $errorCollection);
    }
}
