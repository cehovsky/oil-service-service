<?php

declare(strict_types=1);

namespace App\OilService;

use App\Domain\Error\ErrorCollection;
use App\Domain\Error\ErrorItem;
use App\Domain\Exception\ValidationException;
use App\OilService\DBAL\Entity\CustomerCar;
use App\OilService\DBAL\Entity\CustomerCarHistory;
use App\OilService\DBAL\Entity\User;
use App\OilService\DBAL\Enum\CustomerCarBrandEnum;
use App\OilService\Factory\EntityFactory;
use App\VehicleDataCube\VehicleDataCubeService;
use Doctrine\ORM\EntityManagerInterface;

class CustomerCarService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly VehicleDataCubeService $vehicleDataCubeService,
    ) {
    }

    public function createCustomerCar(
        string $licensePlate,
        ?CustomerCarBrandEnum $brand,
        ?string $model,
        ?string $vin,
        ?User $user,
    ): CustomerCar {
        $car = $this->entityFactory->createCustomerCar(
            $licensePlate,
            $brand,
            $model,
            $vin,
            $user,
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
    ): CustomerCar {
        $car->setLicensePlate($licensePlate);
        $car->setBrand($brand);
        $car->setModel($model);
        $car->setVin($vin);

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

        $this->entityManager->flush();

        return true;
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
