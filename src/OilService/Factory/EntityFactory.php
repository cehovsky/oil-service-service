<?php

declare(strict_types=1);

namespace App\OilService\Factory;

use App\OilService\DBAL\Entity\Car;
use App\OilService\DBAL\Entity\Form;
use App\OilService\DBAL\Entity\Route;
use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Entity\User;
use App\OilService\DBAL\Enum\CarStatusEnum;
use App\OilService\DBAL\Enum\FormStatusEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Repository\FormRepository;
use DateTimeImmutable;
use Symfony\Component\Uid\Factory\UuidFactory;

class EntityFactory
{
    public function __construct(
        private readonly UuidFactory $uuidFactory,
        private readonly FormRepository $formRepository,
    ) {
    }

    public function createUser(
        string $email,
        string $phone,
        string $fullName,
    ): User {
        return new User(
            $this->uuidFactory->timeBased()->create(),
            $email,
            $phone,
            $fullName,
            new DateTimeImmutable(),
        );
    }

    public function createForm(
        string $fullName,
        string $phone,
        string $email,
        string $carModel,
        string $licensePlate,
        string $address,
        ?string $note,
        bool $isCompany,
        ?string $companyName,
        ?string $companyIdentificationNumber,
        ?string $companyTaxId,
        ?string $companyAddress,
        FormStatusEnum $status,
        RealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        User $user,
        ?Route $route = null,
    ): Form {
        return new Form(
            $this->uuidFactory->timeBased()->create(),
            $this->formRepository->getNextIdent(),
            $fullName,
            $phone,
            $email,
            $carModel,
            $licensePlate,
            $address,
            $note,
            $isCompany,
            $companyName,
            $companyIdentificationNumber,
            $companyTaxId,
            $companyAddress,
            $status,
            $realizationTimeSlot,
            $realizationDate,
            $user,
            new DateTimeImmutable(),
            $route,
        );
    }

    public function createTerm(
        DateTimeImmutable $date,
        RealizationTimeSlotEnum $timeSlot,
        bool $isActive,
        int $maxCount,
    ): Term {
        return new Term(
            $this->uuidFactory->timeBased()->create(),
            $date,
            $timeSlot,
            $isActive,
            $maxCount,
            new DateTimeImmutable(),
        );
    }

    public function createRoute(
        ?Car $car,
        bool $isActive,
        DateTimeImmutable $date,
    ): Route {
        return new Route(
            $this->uuidFactory->timeBased()->create(),
            $car,
            $isActive,
            $date,
            new DateTimeImmutable(),
        );
    }

    public function createCar(
        string $label,
        string $ident,
        string $licensePlate,
        CarStatusEnum $status,
    ): Car {
        return new Car(
            $this->uuidFactory->timeBased()->create(),
            $label,
            $ident,
            $licensePlate,
            $status,
            new DateTimeImmutable(),
        );
    }
}
