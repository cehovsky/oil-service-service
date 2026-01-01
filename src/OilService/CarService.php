<?php

declare(strict_types=1);

namespace App\OilService;

use App\Domain\Error\ErrorCollection;
use App\Domain\Error\ErrorItem;
use App\Domain\Exception\ValidationException;
use App\OilService\DBAL\Entity\Car;
use App\OilService\DBAL\Enum\CarStatusEnum;
use App\OilService\Factory\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;

class CarService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createCar(
        string $label,
        string $ident,
        string $licensePlate,
        CarStatusEnum $status,
    ): Car {
        $car = $this->entityFactory->createCar(
            $label,
            $ident,
            $licensePlate,
            $status,
        );

        $this->entityManager->persist($car);
        $this->entityManager->flush();

        return $car;
    }

    public function updateCar(
        Car $car,
        string $label,
        string $ident,
        string $licensePlate,
        CarStatusEnum $status,
    ): Car {
        $car->setLabel($label);
        $car->setIdent($ident);
        $car->setLicensePlate($licensePlate);
        $car->setStatus($status);

        $this->entityManager->flush();

        return $car;
    }

    public function deleteCar(Car $car): void
    {
        if ($car->getRoutes()->count() > 0) {
            throw $this->createCarAssignedException();
        }

        $this->entityManager->remove($car);
        $this->entityManager->flush();
    }

    private function createCarAssignedException(): ValidationException
    {
        $errorCollection = new ErrorCollection();
        $errorCollection->add(
            new ErrorItem(
                'Car is assigned to routes. Remove it from routes or change the car status first.',
                'carHasRoutes',
                null,
            )
        );

        return new ValidationException(errorCollection: $errorCollection);
    }
}
