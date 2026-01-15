<?php

declare(strict_types=1);

namespace App\OilService\Factory;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\OilService\DBAL\Entity\Car;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Entity\PriceListItem;
use App\OilService\DBAL\Entity\Route;
use App\OilService\DBAL\Entity\RouteUser;
use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Entity\User;
use App\OilService\DBAL\Enum\CarStatusEnum;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Repository\OrderRepository;
use DateTimeImmutable;
use Symfony\Component\Uid\Factory\UuidFactory;

class EntityFactory
{
    public function __construct(
        private readonly UuidFactory $uuidFactory,
        private readonly OrderRepository $orderRepository,
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

    public function createOrder(
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
        OrderStatusEnum $status,
        RealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        User $user,
        ?Route $route = null,
    ): Order {
        return new Order(
            $this->uuidFactory->timeBased()->create(),
            $this->orderRepository->getNextIdent(),
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

    public function createRouteUser(Route $route, AuthUser $user): RouteUser
    {
        return new RouteUser(
            $this->uuidFactory->timeBased()->create(),
            $route,
            $user,
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

    public function createPriceListItem(
        string $label,
        ?string $description,
        ?string $invoiceLabel,
        string $price,
        int $vat,
        string $priceVat,
        bool $isActive,
        bool $isPublic,
        bool $isDefault,
        bool $isHiddenOnInvoice,
        string $code,
        ?string $brand,
        ?string $externalCode,
    ): PriceListItem {
        return new PriceListItem(
            $this->uuidFactory->timeBased()->create(),
            $label,
            $description,
            $invoiceLabel,
            $price,
            $vat,
            $priceVat,
            $isActive,
            $isPublic,
            $isDefault,
            $isHiddenOnInvoice,
            $code,
            $brand,
            $externalCode,
            new DateTimeImmutable(),
        );
    }
}
