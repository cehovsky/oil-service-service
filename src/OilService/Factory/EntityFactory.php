<?php

declare(strict_types=1);

namespace App\OilService\Factory;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\OilService\DBAL\Entity\Car;
use App\OilService\DBAL\Entity\ChatKnowledgeItem;
use App\OilService\DBAL\Entity\ChatMessage;
use App\OilService\DBAL\Entity\ChatSession;
use App\OilService\DBAL\Entity\ChatUserRequest;
use App\OilService\DBAL\Entity\CustomerCar;
use App\CarDatabase\DBAL\Entity\Engine as CarDatabaseEngine;
use App\OilService\DBAL\Entity\CustomerCarHistory;
use App\OilService\DBAL\Entity\InventoryItem;
use App\OilService\DBAL\Entity\InventoryItemHistory;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Entity\OrderInventoryItem;
use App\OilService\DBAL\Entity\PriceListItem;
use App\OilService\DBAL\Entity\Route;
use App\OilService\DBAL\Entity\RouteUser;
use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Entity\User;
use App\Files\DBAL\Entity\File;
use App\OilService\DBAL\Enum\ChatKnowledgeItemTypeEnum;
use App\OilService\DBAL\Enum\ChatMessageRoleEnum;
use App\OilService\DBAL\Enum\ChatSessionStatusEnum;
use App\OilService\DBAL\Enum\ChatUserRequestStatusEnum;
use App\OilService\DBAL\Enum\CarStatusEnum;
use App\CarDatabase\DBAL\Enum\CustomerCarBrandEnum;
use App\OilService\DBAL\Enum\InventoryMovementTypeEnum;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Repository\ChatSessionRepository;
use App\OilService\DBAL\Repository\ChatUserRequestRepository;
use App\OilService\DBAL\Repository\OrderRepository;
use DateTimeImmutable;
use Symfony\Component\Uid\Factory\UuidFactory;

class EntityFactory
{
    public function __construct(
        private readonly UuidFactory $uuidFactory,
        private readonly OrderRepository $orderRepository,
        private readonly ChatSessionRepository $chatSessionRepository,
        private readonly ChatUserRequestRepository $chatUserRequestRepository,
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

    /**
     * @param File[] $otherPhotos
     */
    public function createOrder(
        string $fullName,
        string $phone,
        string $email,
        string $carModel,
        string $licensePlate,
        ?string $vin,
        string $address,
        ?string $note,
        bool $isCompany,
        ?string $companyName,
        ?string $companyIdentificationNumber,
        ?string $companyTaxId,
        ?string $companyAddress,
        ?File $oilChangeVehiclePhoto,
        ?File $vinPhoto,
        ?File $oldOilFilterPhoto,
        ?File $oldOilPhoto,
        ?File $odometerPhoto,
        ?int $mileage,
        array $otherPhotos,
        OrderStatusEnum $status,
        RealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        User $user,
        ?Route $route = null,
        ?float $latitude = null,
        ?float $longitude = null,
        ?bool $isWithinServiceArea = null,
        ?CustomerCar $customerCar = null,
    ): Order {
        return new Order(
            $this->uuidFactory->timeBased()->create(),
            $this->orderRepository->getNextIdent(),
            $this->uuidFactory->randomBased()->create()->toRfc4122(),
            $fullName,
            $phone,
            $email,
            $carModel,
            $licensePlate,
            $vin,
            $address,
            $note,
            $isCompany,
            $companyName,
            $companyIdentificationNumber,
            $companyTaxId,
            $companyAddress,
            $oilChangeVehiclePhoto,
            $vinPhoto,
            $oldOilFilterPhoto,
            $oldOilPhoto,
            $odometerPhoto,
            $mileage,
            $otherPhotos,
            $status,
            $realizationTimeSlot,
            $realizationDate,
            $user,
            new DateTimeImmutable(),
            $route,
            $latitude,
            $longitude,
            $isWithinServiceArea,
            $customerCar,
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

    public function createCustomerCar(
        string $licensePlate,
        ?CustomerCarBrandEnum $brand = null,
        ?string $model = null,
        ?string $vin = null,
        ?User $user = null,
        ?CarDatabaseEngine $engine = null,
    ): CustomerCar {
        return new CustomerCar(
            $this->uuidFactory->timeBased()->create(),
            $licensePlate,
            new DateTimeImmutable(),
            $brand,
            $model,
            $vin,
            $user,
            $engine,
        );
    }

    public function createCustomerCarHistory(CustomerCar $car, User $user): CustomerCarHistory
    {
        return new CustomerCarHistory(
            $this->uuidFactory->timeBased()->create(),
            $car,
            $user,
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

    public function createInventoryItem(
        string $label,
        ?string $description,
        string $code,
        ?string $oemCode,
        ?string $price,
        ?int $vat,
        ?string $priceVat,
        int $stockCount,
        AuthUser $createdBy,
        AuthUser $updatedBy,
    ): InventoryItem {
        $now = new DateTimeImmutable();

        return new InventoryItem(
            $this->uuidFactory->timeBased()->create(),
            $label,
            $description,
            $code,
            $oemCode,
            $price,
            $vat,
            $priceVat,
            $stockCount,
            $createdBy,
            $updatedBy,
            $now,
            $now,
        );
    }

    public function createInventoryItemHistory(
        InventoryItem $inventoryItem,
        InventoryMovementTypeEnum $movementType,
        int $quantity,
        bool $isIncrement,
        AuthUser $createdBy,
        ?Order $order = null,
        ?string $price = null,
        ?int $vat = null,
        ?string $priceVat = null,
        ?string $note = null,
        ?DateTimeImmutable $createdAt = null,
    ): InventoryItemHistory {
        $createdAt ??= new DateTimeImmutable();

        return new InventoryItemHistory(
            $this->uuidFactory->timeBased()->create(),
            $inventoryItem,
            $movementType,
            $quantity,
            $isIncrement,
            $createdBy,
            $createdAt,
            $order,
            $price,
            $vat,
            $priceVat,
            $note,
        );
    }

    public function createOrderInventoryItem(
        Order $order,
        InventoryItem $inventoryItem,
        int $quantity,
        ?DateTimeImmutable $createdAt = null,
    ): OrderInventoryItem {
        $createdAt ??= new DateTimeImmutable();

        return new OrderInventoryItem(
            $this->uuidFactory->timeBased()->create(),
            $order,
            $inventoryItem,
            $quantity,
            $createdAt,
            $createdAt,
        );
    }

    public function createChatSession(
        string $language,
        ChatSessionStatusEnum $status,
    ): ChatSession {
        $now = new DateTimeImmutable();

        return new ChatSession(
            $this->uuidFactory->timeBased()->create(),
            $this->chatSessionRepository->getNextIdent(),
            $language,
            $status,
            $now,
            $now,
        );
    }

    public function createChatMessage(
        ChatSession $session,
        ChatMessageRoleEnum $role,
        string $content,
    ): ChatMessage {
        $createdAt = new DateTimeImmutable();

        return new ChatMessage(
            $this->uuidFactory->timeBased()->create(),
            $session,
            $role,
            $content,
            $createdAt,
        );
    }

    public function createChatKnowledgeItem(
        string $name,
        string $content,
        ChatKnowledgeItemTypeEnum $type,
        string $language,
        bool $isActive,
    ): ChatKnowledgeItem {
        $now = new DateTimeImmutable();

        return new ChatKnowledgeItem(
            $this->uuidFactory->timeBased()->create(),
            $name,
            $content,
            $type,
            $language,
            $isActive,
            $now,
            $now,
        );
    }

    public function createChatUserRequest(
        ?ChatSession $session,
        string $content,
    ): ChatUserRequest {
        return new ChatUserRequest(
            $this->uuidFactory->timeBased()->create(),
            $this->chatUserRequestRepository->getNextIdent(),
            $session,
            $content,
            ChatUserRequestStatusEnum::OPEN,
            false,
            new DateTimeImmutable(),
            null,
        );
    }
}
