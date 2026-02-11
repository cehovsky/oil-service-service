<?php

declare(strict_types=1);

namespace App\Modules\OilService\Factory;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Domain\DTOValueResolver;
use App\Domain\Exception\InvalidArgumentException;
use App\Modules\OilService\DTO\OrderCreateResponseDTO;
use App\Modules\OilService\DTO\OrderCoordinatesResolveResponseDTO;
use App\Modules\OilService\DTO\OrderDeleteResponseDTO;
use App\Modules\OilService\DTO\OrderDTO;
use App\Modules\OilService\DTO\OrderDTOCollection;
use App\Modules\OilService\DTO\OrderInfoResponseDTO;
use App\Modules\OilService\DTO\OrderListResponseDTO;
use App\Modules\OilService\DTO\OrderUpdateResponseDTO;
use App\Modules\OilService\DTO\PriceListItemCreateResponseDTO;
use App\Modules\OilService\DTO\PriceListItemDTO;
use App\Modules\OilService\DTO\PriceListItemDeleteResponseDTO;
use App\Modules\OilService\DTO\PriceListItemInfoResponseDTO;
use App\Modules\OilService\DTO\PriceListItemListResponseDTO;
use App\Modules\OilService\DTO\PriceListItemPublicDTO;
use App\Modules\OilService\DTO\PriceListItemPublicListResponseDTO;
use App\Modules\OilService\DTO\PriceListItemUpdateResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserCreateResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserDeleteResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserDTO;
use App\Modules\OilService\DTO\OilServiceUserDTOCollection;
use App\Modules\OilService\DTO\OilServiceUserInfoResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserListDTO;
use App\Modules\OilService\DTO\OilServiceUserListResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserUpdateResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserWithOrdersDTO;
use App\Modules\OilService\DTO\AvailableTermDTO;
use App\Modules\OilService\DTO\AvailableTermListResponseDTO;
use App\Modules\OilService\DTO\TermDTO;
use App\Modules\OilService\DTO\TermCreateResponseDTO;
use App\Modules\OilService\DTO\TermUpdateResponseDTO;
use App\Modules\OilService\DTO\TermDeleteResponseDTO;
use App\Modules\OilService\DTO\TermInfoResponseDTO;
use App\Modules\OilService\DTO\TermListResponseDTO;
use App\Modules\OilService\DTO\TermWithOrderCountDTO;
use App\Modules\OilService\DTO\TermWithOrderCountListResponseDTO;
use App\Modules\OilService\DTO\RouteDTO;
use App\Modules\OilService\DTO\RouteCreateResponseDTO;
use App\Modules\OilService\DTO\RouteUpdateResponseDTO;
use App\Modules\OilService\DTO\RouteDeleteResponseDTO;
use App\Modules\OilService\DTO\RouteInfoResponseDTO;
use App\Modules\OilService\DTO\RouteListResponseDTO;
use App\Modules\OilService\DTO\RouteTermDTO;
use App\Modules\CarApp\DTO\CarAppRouteDTO;
use App\Modules\CarApp\DTO\CarAppRouteListResponseDTO;
use App\Modules\OilService\DTO\OrderStorageContainerMaterialDTO;
use App\Modules\OilService\DTO\OrderInventoryItemDTO;
use App\Modules\OilService\DTO\OrderSummaryDTO;
use App\Modules\OilService\DTO\InventoryItemCreateResponseDTO;
use App\Modules\OilService\DTO\InventoryItemDTO;
use App\Modules\OilService\DTO\InventoryItemDeleteResponseDTO;
use App\Modules\OilService\DTO\InventoryItemHistoryDTO;
use App\Modules\OilService\DTO\InventoryItemInfoResponseDTO;
use App\Modules\OilService\DTO\InventoryItemListResponseDTO;
use App\Modules\OilService\DTO\InventoryItemStockInResponseDTO;
use App\Modules\OilService\DTO\InventoryItemStockOutResponseDTO;
use App\Modules\OilService\DTO\InventoryItemSummaryDTO;
use App\Modules\OilService\DTO\InventoryItemUpdateResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialDTO;
use App\Modules\Warehouse\DTO\StorageContainerSummaryDTO;
use App\Modules\Warehouse\DTO\WarehouseSummaryDTO;
use App\Modules\Warehouse\DTO\RecyclingSummaryDTO;
use App\Modules\Warehouse\DTO\RouteSummaryDTO as WarehouseRouteSummaryDTO;
use App\Modules\Warehouse\DTO\WasteMaterialSummaryDTO;
use App\Modules\Warehouse\Factory\DTOFactory as WarehouseDTOFactory;
use App\Modules\OilService\DTO\ChatDefaultMessageResponseDTO;
use App\Modules\OilService\DTO\ChatMessageDTO;
use App\Modules\OilService\DTO\ChatMessageListResponseDTO;
use App\Modules\OilService\DTO\ChatMessageResponseDTO;
use App\Modules\OilService\DTO\ChatSessionCompleteResponseDTO;
use App\Modules\OilService\DTO\ChatSessionCreateResponseDTO;
use App\Modules\OilService\DTO\ChatKnowledgeItemDTO;
use App\Modules\OilService\DTO\ChatKnowledgeItemCreateResponseDTO;
use App\Modules\OilService\DTO\ChatKnowledgeItemUpdateResponseDTO;
use App\Modules\OilService\DTO\ChatKnowledgeItemDeleteResponseDTO;
use App\Modules\OilService\DTO\ChatKnowledgeItemListResponseDTO;
use App\Modules\OilService\DTO\ChatKnowledgeItemInfoResponseDTO;
use App\Modules\OilService\DTO\ChatUserRequestDTO;
use App\Modules\OilService\DTO\ChatUserRequestDetailDTO;
use App\Modules\OilService\DTO\ChatUserRequestDeleteResponseDTO;
use App\Modules\OilService\DTO\ChatUserRequestListResponseDTO;
use App\Modules\OilService\DTO\ChatUserRequestInfoResponseDTO;
use App\Modules\OilService\DTO\ChatUserRequestResolveResponseDTO;
use App\Modules\OilService\DTO\ChatUserRequestUpdateResponseDTO;
use App\Modules\OilService\DTO\ChatSessionDTO;
use App\Modules\OilService\DTO\ChatSessionDetailDTO;
use App\Modules\OilService\DTO\ChatSessionInfoResponseDTO;
use App\Modules\OilService\DTO\ChatSessionListResponseDTO;
use App\Modules\OilService\DTO\ChatSessionLightDTO;
use App\Modules\OilService\DTO\CarDTO;
use App\Modules\OilService\DTO\CarCreateResponseDTO;
use App\Modules\OilService\DTO\CarUpdateResponseDTO;
use App\Modules\OilService\DTO\CarDeleteResponseDTO;
use App\Modules\OilService\DTO\CarInfoResponseDTO;
use App\Modules\OilService\DTO\CarListResponseDTO;
use App\Modules\OilService\DTO\CustomerCarDTO;
use App\Modules\OilService\DTO\CustomerCarDetailDTO;
use App\Modules\OilService\DTO\CustomerCarCreateResponseDTO;
use App\Modules\OilService\DTO\CustomerCarUpdateResponseDTO;
use App\Modules\OilService\DTO\CustomerCarDeleteResponseDTO;
use App\Modules\OilService\DTO\CustomerCarInfoResponseDTO;
use App\Modules\OilService\DTO\CustomerCarListResponseDTO;
use App\Modules\OilService\DTO\CustomerCarHistoryDTO;
use App\Modules\OilService\DTO\CustomerCarHistoryDeleteResponseDTO;
use App\Modules\OilService\DTO\CustomerCarEngineFilterDTO;
use App\Modules\OilService\DTO\OrderCustomerCarResolveResponseDTO;
use App\Modules\OilService\DTO\OrderCustomerCarConflictResponseDTO;
use App\Modules\Users\DTO\UserDTO;
use App\Modules\CarDatabase\DTO\EngineSummaryDTO;
use App\Modules\CarDatabase\DTO\FilterSummaryDTO;
use App\OilService\DBAL\Entity\Car;
use App\OilService\DBAL\Entity\ChatKnowledgeItem;
use App\OilService\DBAL\Entity\ChatMessage;
use App\OilService\DBAL\Entity\ChatSession;
use App\OilService\DBAL\Entity\ChatUserRequest;
use App\OilService\DBAL\Entity\InventoryItem;
use App\OilService\DBAL\Entity\InventoryItemHistory;
use App\OilService\DBAL\Entity\Order;
use App\Files\DBAL\Entity\File as FileEntity;
use App\Modules\Files\DTO\FileDTO;
use App\OilService\DBAL\Entity\OrderInventoryItem;
use App\OilService\DBAL\Entity\PriceListItem;
use App\OilService\DBAL\Entity\Route;
use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Entity\User;
use App\OilService\DBAL\Entity\CustomerCar;
use App\OilService\DBAL\Entity\CustomerCarHistory;
use App\CarDatabase\DBAL\Entity\Engine as CarDatabaseEngine;
use App\CarDatabase\DBAL\Entity\Filter as CarDatabaseFilter;
use App\CarDatabase\DBAL\Entity\EngineFilter as CarDatabaseEngineFilter;
use App\OilService\DBAL\Enum\ChatMessageRoleEnum;
use App\Warehouse\DBAL\Entity\StorageContainer;
use App\Warehouse\DBAL\Entity\StorageContainerMaterial;
use App\Warehouse\DBAL\Entity\Recycling;
use App\Warehouse\DBAL\Entity\Warehouse;
use App\Warehouse\DBAL\Entity\WasteMaterial;
use DateTimeInterface;

class DTOFactory
{
    public function createOrderCreateResponseDTO(): OrderCreateResponseDTO
    {
        return new OrderCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            true,
        );
    }

    public function createOilServiceUserDTO(User $user): OilServiceUserDTO
    {
        $cars = $this->createCustomerCarDTOs($user->getCars()->toArray());

        return new OilServiceUserDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getFullName(),
            $user->getCreatedAt()->format(DateTimeInterface::ATOM),
            $cars,
        );
    }

    private function createOilServiceUserDTOWithoutCars(User $user): OilServiceUserDTO
    {
        return new OilServiceUserDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getFullName(),
            $user->getCreatedAt()->format(DateTimeInterface::ATOM),
            [],
        );
    }

    public function createOilServiceUserListDTO(User $user): OilServiceUserListDTO
    {
        $cars = $this->createCustomerCarDTOs($user->getCars()->toArray());

        return new OilServiceUserListDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getFullName(),
            $user->getCreatedAt()->format(DateTimeInterface::ATOM),
            $user->getOrders()->count(),
            $cars,
        );
    }

    public function createOilServiceUserWithOrdersDTO(User $user): OilServiceUserWithOrdersDTO
    {
        /** @var OrderDTO[] $orders */
        $orders = $this->createOrderDTOCollection($user->getOrders()->toArray())->toArray();
        $cars = $this->createCustomerCarDTOs($user->getCars()->toArray());

        return new OilServiceUserWithOrdersDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getFullName(),
            $user->getCreatedAt()->format(DateTimeInterface::ATOM),
            $orders,
            $cars,
        );
    }

    public function createOrderDTO(Order $order): OrderDTO
    {
        $route = $order->getRoute();
        $materials = $this->createOrderStorageContainerMaterialDTOs($order);
        $inventoryItems = $this->createOrderInventoryItemDTOs($order);
        $priceListItems = $this->createPriceListItemDTOs($order->getPriceListItems()->toArray());
        $otherPhotos = $this->createFileDTOs($order->getOtherPhotos()->toArray());
        $customerCar = $order->getCustomerCar();

        return new OrderDTO(
            $order->getId()->__toString(),
            $order->getFormattedIdent(),
            $order->getFullName(),
            $order->getPhone(),
            $order->getEmail(),
            $order->getCarModel(),
            $order->getLicensePlate(),
            $order->getVin(),
            $order->getAddress(),
            $order->getLatitude(),
            $order->getLongitude(),
            $order->getNote(),
            $order->getIsCompany(),
            $order->getCompanyName(),
            $order->getCompanyIdentificationNumber(),
            $order->getCompanyTaxId(),
            $order->getCompanyAddress(),
            $this->createFileDTO($order->getOilChangeVehiclePhoto()),
            $this->createFileDTO($order->getVinPhoto()),
            $this->createFileDTO($order->getOldOilFilterPhoto()),
            $this->createFileDTO($order->getOldOilPhoto()),
            $this->createFileDTO($order->getOdometerPhoto()),
            $otherPhotos,
            $order->getStatus()->value,
            $order->getRealizationTimeSlot()->value,
            $order->getRealizationDate()->format('Y-m-d'),
            $order->getCreatedAt()->format(DateTimeInterface::ATOM),
            $this->createOilServiceUserDTO($order->getUser()),
            $route ? $this->createRouteDTO($route) : null,
            $materials,
            $inventoryItems,
            $priceListItems,
            $customerCar ? $this->createCustomerCarDTO($customerCar) : null,
        );
    }

    /**
     * @param Order[] $orders
     *
     * @throws InvalidArgumentException
     */
    public function createOrderDTOCollection(array $orders): OrderDTOCollection
    {
        $collection = new OrderDTOCollection();

        foreach ($orders as $order) {
            $collection->add($this->createOrderDTO($order));
        }

        return $collection;
    }

    /**
     * @param Order[] $orders
     *
     * @throws InvalidArgumentException
     */
    public function createOrderListResponseDTO(array $orders, int $pageCount): OrderListResponseDTO
    {
        /** @var OrderDTO[] $orderDTOs */
        $orderDTOs = $this->createOrderDTOCollection($orders)->toArray();

        return new OrderListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $orderDTOs,
            $pageCount,
        );
    }

    public function createOrderInfoResponseDTO(Order $order): OrderInfoResponseDTO
    {
        return new OrderInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createOrderDTO($order),
        );
    }

    public function createOrderUpdateResponseDTO(Order $order): OrderUpdateResponseDTO
    {
        return new OrderUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createOrderDTO($order),
        );
    }

    public function createOrderCoordinatesResolveResponseDTO(
        Order $order,
        bool $success,
        ?string $message,
    ): OrderCoordinatesResolveResponseDTO {
        return new OrderCoordinatesResolveResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $success,
            $message,
            $this->createOrderDTO($order),
        );
    }

    public function createOrderDeleteResponseDTO(): OrderDeleteResponseDTO
    {
        return new OrderDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createAvailableTermDTO(Term $term): AvailableTermDTO
    {
        return new AvailableTermDTO(
            $term->getDate()->format('Y-m-d'),
            $term->getTimeSlot()->value,
        );
    }

    /**
     * @param Term[] $terms
     */
    public function createAvailableTermListResponseDTO(array $terms): AvailableTermListResponseDTO
    {
        $termDTOs = [];

        foreach ($terms as $term) {
            $termDTOs[] = $this->createAvailableTermDTO($term);
        }

        return new AvailableTermListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $termDTOs,
        );
    }

    /**
     * @param User[] $users
     *
     * @throws InvalidArgumentException
     */
    public function createOilServiceUserDTOCollection(array $users): OilServiceUserDTOCollection
    {
        $collection = new OilServiceUserDTOCollection();

        foreach ($users as $user) {
            $collection->add($this->createOilServiceUserListDTO($user));
        }

        return $collection;
    }

    /**
     * @param User[] $users
     *
     * @throws InvalidArgumentException
     */
    public function createOilServiceUserListResponseDTO(array $users, int $pageCount): OilServiceUserListResponseDTO
    {
        /** @var OilServiceUserListDTO[] $userDTOs */
        $userDTOs = $this->createOilServiceUserDTOCollection($users)->toArray();

        return new OilServiceUserListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $userDTOs,
            $pageCount,
        );
    }

    public function createOilServiceUserInfoResponseDTO(User $user): OilServiceUserInfoResponseDTO
    {
        return new OilServiceUserInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createOilServiceUserWithOrdersDTO($user),
        );
    }

    public function createOilServiceUserCreateResponseDTO(User $user): OilServiceUserCreateResponseDTO
    {
        return new OilServiceUserCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createOilServiceUserDTO($user),
        );
    }

    public function createOilServiceUserUpdateResponseDTO(User $user): OilServiceUserUpdateResponseDTO
    {
        return new OilServiceUserUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createOilServiceUserDTO($user),
        );
    }

    public function createOilServiceUserDeleteResponseDTO(): OilServiceUserDeleteResponseDTO
    {
        return new OilServiceUserDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createTermDTO(Term $term): TermDTO
    {
        $routes = [];

        foreach ($term->getRoutes() as $route) {
            $routes[] = $this->createRouteDTO($route);
        }

        return new TermDTO(
            $term->getId()->__toString(),
            $term->getDate()->format('Y-m-d'),
            $term->getTimeSlot()->value,
            $term->getIsActive(),
            $term->getMaxCount(),
            $term->getCreatedAt()->format(DateTimeInterface::ATOM),
            $routes,
        );
    }

    /**
     * @param Term[] $terms
     */
    public function createTermListResponseDTO(array $terms, int $pageCount): TermListResponseDTO
    {
        $termDTOs = [];

        foreach ($terms as $term) {
            $termDTOs[] = $this->createTermDTO($term);
        }

        return new TermListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $termDTOs,
            $pageCount,
        );
    }

    public function createTermInfoResponseDTO(Term $term): TermInfoResponseDTO
    {
        return new TermInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createTermDTO($term),
        );
    }

    public function createTermCreateResponseDTO(Term $term): TermCreateResponseDTO
    {
        return new TermCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createTermDTO($term),
        );
    }

    public function createTermUpdateResponseDTO(Term $term): TermUpdateResponseDTO
    {
        return new TermUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createTermDTO($term),
        );
    }

    public function createTermDeleteResponseDTO(): TermDeleteResponseDTO
    {
        return new TermDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    /**
     * @param UserDTO[] $users
     */
    public function createTermWithOrderCountDTO(Term $term, int $orderCount, array $users): TermWithOrderCountDTO
    {
        return new TermWithOrderCountDTO(
            $term->getId()->__toString(),
            $term->getDate()->format('Y-m-d'),
            $term->getTimeSlot()->value,
            $term->getIsActive(),
            $term->getMaxCount(),
            $orderCount,
            $term->getCreatedAt()->format(DateTimeInterface::ATOM),
            $users,
        );
    }

    /**
     * @param Term[] $terms
     * @param array<string, int> $orderCounts
     */
    public function createTermWithOrderCountListResponseDTO(array $terms, array $orderCounts): TermWithOrderCountListResponseDTO
    {
        $termDTOs = [];

        foreach ($terms as $term) {
            $key = $term->getDate()->format('Y-m-d') . '|' . $term->getTimeSlot()->value;
            $orderCount = $orderCounts[$key] ?? 0;
            $users = $this->createRouteUserDTOsFromTerm($term);

            $termDTOs[] = $this->createTermWithOrderCountDTO($term, $orderCount, $users);
        }

        return new TermWithOrderCountListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $termDTOs,
        );
    }

    public function createRouteDTO(Route $route): RouteDTO
    {
        $car = $route->getCar();
        $routeTerms = [];
        $storageContainers = [];
        /** @var StorageContainerMaterialDTO[] $storageContainerMaterials */
        $storageContainerMaterials = [];
        $routeUsers = [];
        $orders = [];

        $warehouseDTOFactory = new WarehouseDTOFactory();

        foreach ($route->getTerms() as $term) {
            $routeTerms[] = $this->createRouteTermDTO($term);
        }

        foreach ($route->getStorageContainerLocations() as $location) {
            $storageContainer = $location->getStorageContainer();

            $storageContainers[$storageContainer->getId()->__toString()] = $this->createStorageContainerSummaryDTO(
                $storageContainer
            );
        }

        foreach ($route->getStorageContainerMaterials() as $storageContainerMaterial) {
            $storageContainerMaterials[] = $warehouseDTOFactory->createStorageContainerMaterialDTO($storageContainerMaterial);
        }

        foreach ($route->getRouteUsers() as $routeUser) {
            $routeUsers[] = $this->createAuthUserDTO($routeUser->getUser());
        }

        foreach ($this->sortRouteOrders($route->getOrders()->toArray()) as $order) {
            $orders[] = $this->createOrderSummaryDTO($order);
        }

        return new RouteDTO(
            $route->getId()->__toString(),
            $car ? $this->createCarDTO($car) : null,
            $route->getIsActive(),
            $route->getDate()->format('Y-m-d'),
            $route->getCreatedAt()->format(DateTimeInterface::ATOM),
            $routeTerms,
            array_values($storageContainers),
            $storageContainerMaterials,
            $routeUsers,
            $orders,
        );
    }

    /**
     * @param Route[] $routes
     */
    public function createCarAppRouteListResponseDTO(array $routes, int $pageCount): CarAppRouteListResponseDTO
    {
        $routeDTOs = [];

        foreach ($routes as $route) {
            $routeDTOs[] = $this->createCarAppRouteDTO($route);
        }

        return new CarAppRouteListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $routeDTOs,
            $pageCount,
        );
    }

    public function createCarAppRouteDTO(Route $route): CarAppRouteDTO
    {
        $car = $route->getCar();
        $routeTerms = [];
        $storageContainers = [];
        /** @var StorageContainerMaterialDTO[] $storageContainerMaterials */
        $storageContainerMaterials = [];
        $routeUsers = [];
        $orders = [];

        $warehouseDTOFactory = new WarehouseDTOFactory();

        foreach ($route->getTerms() as $term) {
            $routeTerms[] = $this->createRouteTermDTO($term);
        }

        foreach ($route->getStorageContainerLocations() as $location) {
            $storageContainer = $location->getStorageContainer();

            $storageContainers[$storageContainer->getId()->__toString()] = $this->createStorageContainerSummaryDTO(
                $storageContainer
            );
        }

        foreach ($route->getStorageContainerMaterials() as $storageContainerMaterial) {
            $storageContainerMaterials[] = $warehouseDTOFactory->createStorageContainerMaterialDTO($storageContainerMaterial);
        }

        foreach ($route->getRouteUsers() as $routeUser) {
            $routeUsers[] = $this->createAuthUserDTO($routeUser->getUser());
        }

        foreach ($this->sortRouteOrders($route->getOrders()->toArray()) as $order) {
            $orders[] = $this->createOrderDTO($order);
        }

        return new CarAppRouteDTO(
            $route->getId()->__toString(),
            $car ? $this->createCarDTO($car) : null,
            $route->getIsActive(),
            $route->getDate()->format('Y-m-d'),
            $route->getCreatedAt()->format(DateTimeInterface::ATOM),
            $routeTerms,
            array_values($storageContainers),
            $storageContainerMaterials,
            $routeUsers,
            $orders,
        );
    }

    /**
     * @return OrderStorageContainerMaterialDTO[]
     */
    private function createOrderStorageContainerMaterialDTOs(Order $order): array
    {
        $materials = [];

        foreach ($order->getStorageContainerMaterials() as $material) {
            $materials[] = $this->createOrderStorageContainerMaterialDTO($material);
        }

        return $materials;
    }

    private function createOrderStorageContainerMaterialDTO(StorageContainerMaterial $material): OrderStorageContainerMaterialDTO
    {
        $warehouse = $material->getWarehouse();
        $route = $material->getRoute();
        $recycling = $material->getRecycling();

        return new OrderStorageContainerMaterialDTO(
            $material->getId()->__toString(),
            $this->createStorageContainerSummaryDTO($material->getStorageContainer()),
            $this->createWasteMaterialSummaryDTO($material->getWasteMaterial()),
            $warehouse ? $this->createWarehouseSummaryDTO($warehouse) : null,
            $route ? $this->createWarehouseRouteSummaryDTO($route) : null,
            $recycling ? $this->createRecyclingSummaryDTO($recycling) : null,
            $material->getVolume(),
            $material->getIsRecycled(),
            $material->getCreatedAt()->format(DateTimeInterface::ATOM),
            $material->getUpdatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @return OrderInventoryItemDTO[]
     */
    private function createOrderInventoryItemDTOs(Order $order): array
    {
        $items = [];

        foreach ($order->getOrderInventoryItems() as $orderInventoryItem) {
            $items[] = $this->createOrderInventoryItemDTO($orderInventoryItem);
        }

        return $items;
    }

    private function createOrderInventoryItemDTO(OrderInventoryItem $orderInventoryItem): OrderInventoryItemDTO
    {
        $inventoryItem = $orderInventoryItem->getInventoryItem();

        return new OrderInventoryItemDTO(
            $this->createInventoryItemSummaryDTO($inventoryItem),
            $orderInventoryItem->getQuantity(),
        );
    }

    /**
     * @param Order[] $orders
     *
     * @return Order[]
     */
    private function sortRouteOrders(array $orders): array
    {
        usort($orders, function (Order $left, Order $right): int {
            $leftPosition = $left->getRouteOrderPosition() ?? PHP_INT_MAX;
            $rightPosition = $right->getRouteOrderPosition() ?? PHP_INT_MAX;

            if ($leftPosition !== $rightPosition) {
                return $leftPosition <=> $rightPosition;
            }

            $leftCreatedAt = $left->getCreatedAt()->getTimestamp();
            $rightCreatedAt = $right->getCreatedAt()->getTimestamp();

            if ($leftCreatedAt !== $rightCreatedAt) {
                return $leftCreatedAt <=> $rightCreatedAt;
            }

            return $left->getId()->__toString() <=> $right->getId()->__toString();
        });

        return $orders;
    }

    private function createOrderSummaryDTO(Order $order): OrderSummaryDTO
    {
        return new OrderSummaryDTO(
            $order->getId()->__toString(),
            $order->getFormattedIdent(),
            $order->getFullName(),
            $order->getStatus()->value,
            $order->getRealizationDate()->format('Y-m-d'),
            $order->getLatitude(),
            $order->getLongitude(),
        );
    }

    private function createFileDTO(?FileEntity $file): ?FileDTO
    {
        if ($file === null) {
            return null;
        }

        return new FileDTO(
            $file->getId()->__toString(),
            $file->getFileName(),
            $file->getSize(),
            $file->getCreatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @param FileEntity[] $files
     *
     * @return FileDTO[]
     */
    private function createFileDTOs(array $files): array
    {
        $items = [];

        foreach ($files as $file) {
            $items[] = $this->createFileDTO($file);
        }

        return array_values(array_filter($items));
    }

    private function createAuthUserDTO(AuthUser $user): UserDTO
    {
        return new UserDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getFullName(),
            $user->getIsActive(),
            $user->getIsAdmin(),
            $user->getIsOffice(),
        );
    }

    /**
     * @return UserDTO[]
     */
    private function createRouteUserDTOsFromTerm(Term $term): array
    {
        $userDTOs = [];

        foreach ($term->getRoutes() as $route) {
            foreach ($route->getRouteUsers() as $routeUser) {
                $user = $routeUser->getUser();
                $userId = $user->getId()->__toString();

                if (!isset($userDTOs[$userId])) {
                    $userDTOs[$userId] = $this->createAuthUserDTO($user);
                }
            }
        }

        return array_values($userDTOs);
    }

    private function createRouteTermDTO(Term $term): RouteTermDTO
    {
        return new RouteTermDTO(
            $term->getId()->__toString(),
            $term->getDate()->format('Y-m-d'),
            $term->getTimeSlot()->value,
            $term->getIsActive(),
            $term->getMaxCount(),
            $term->getCreatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    private function createStorageContainerSummaryDTO(StorageContainer $storageContainer): StorageContainerSummaryDTO
    {
        $preferredWasteMaterials = [];

        foreach ($storageContainer->getPreferredWasteMaterials() as $preferredWasteMaterial) {
            $preferredWasteMaterials[] = $this->createWasteMaterialSummaryDTO($preferredWasteMaterial);
        }

        return new StorageContainerSummaryDTO(
            $storageContainer->getId()->__toString(),
            $storageContainer->getCode(),
            $storageContainer->getType()->value,
            $storageContainer->getVolumeUnit()->value,
            $preferredWasteMaterials,
        );
    }

    private function createWarehouseSummaryDTO(Warehouse $warehouse): WarehouseSummaryDTO
    {
        return new WarehouseSummaryDTO(
            $warehouse->getId()->__toString(),
            $warehouse->getLabel(),
            $warehouse->getShortLabel(),
            $warehouse->getLatitude(),
            $warehouse->getLongitude(),
            $warehouse->getIsGarage(),
        );
    }

    private function createWarehouseRouteSummaryDTO(Route $route): WarehouseRouteSummaryDTO
    {
        return new WarehouseRouteSummaryDTO(
            $route->getId()->__toString(),
            $route->getDate()->format('Y-m-d'),
            $route->getIsActive(),
        );
    }

    private function createRecyclingSummaryDTO(Recycling $recycling): RecyclingSummaryDTO
    {
        return new RecyclingSummaryDTO(
            $recycling->getId()->__toString(),
            $recycling->getRecycledAt()?->format('Y-m-d'),
            $recycling->getRecycledBy()?->getId()->__toString(),
        );
    }

    private function createWasteMaterialSummaryDTO(WasteMaterial $wasteMaterial): WasteMaterialSummaryDTO
    {
        return new WasteMaterialSummaryDTO(
            $wasteMaterial->getId()->__toString(),
            $wasteMaterial->getCode(),
            $wasteMaterial->getLabel(),
            $wasteMaterial->getVolumeUnit()->value,
        );
    }

    /**
     * @param Route[] $routes
     */
    public function createRouteListResponseDTO(array $routes, int $pageCount): RouteListResponseDTO
    {
        $routeDTOs = [];

        foreach ($routes as $route) {
            $routeDTOs[] = $this->createRouteDTO($route);
        }

        return new RouteListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $routeDTOs,
            $pageCount,
        );
    }

    public function createRouteInfoResponseDTO(Route $route): RouteInfoResponseDTO
    {
        return new RouteInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createRouteDTO($route),
        );
    }

    public function createRouteCreateResponseDTO(Route $route): RouteCreateResponseDTO
    {
        return new RouteCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createRouteDTO($route),
        );
    }

    public function createRouteUpdateResponseDTO(Route $route): RouteUpdateResponseDTO
    {
        return new RouteUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createRouteDTO($route),
        );
    }

    public function createRouteDeleteResponseDTO(): RouteDeleteResponseDTO
    {
        return new RouteDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createPriceListItemDTO(PriceListItem $priceListItem): PriceListItemDTO
    {
        return new PriceListItemDTO(
            $priceListItem->getId()->__toString(),
            $priceListItem->getLabel(),
            $priceListItem->getDescription(),
            $priceListItem->getInvoiceLabel(),
            $priceListItem->getPrice(),
            $priceListItem->getVat(),
            $priceListItem->getPriceVat(),
            $priceListItem->getIsActive(),
            $priceListItem->getIsPublic(),
            $priceListItem->getIsDefault(),
            $priceListItem->getIsHiddenOnInvoice(),
            $priceListItem->getCode(),
            $priceListItem->getBrand(),
            $priceListItem->getExternalCode(),
            $priceListItem->getCreatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @param PriceListItem[] $priceListItems
     *
     * @return PriceListItemDTO[]
     */
    public function createPriceListItemDTOs(array $priceListItems): array
    {
        $dtoItems = [];

        foreach ($priceListItems as $priceListItem) {
            $dtoItems[] = $this->createPriceListItemDTO($priceListItem);
        }

        return $dtoItems;
    }

    /**
     * @param PriceListItem[] $priceListItems
     */
    public function createPriceListItemListResponseDTO(array $priceListItems, int $pageCount): PriceListItemListResponseDTO
    {
        $dtoItems = $this->createPriceListItemDTOs($priceListItems);

        return new PriceListItemListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $dtoItems,
            $pageCount,
        );
    }

    public function createPriceListItemInfoResponseDTO(PriceListItem $priceListItem): PriceListItemInfoResponseDTO
    {
        return new PriceListItemInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createPriceListItemDTO($priceListItem),
        );
    }

    public function createPriceListItemCreateResponseDTO(PriceListItem $priceListItem): PriceListItemCreateResponseDTO
    {
        return new PriceListItemCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createPriceListItemDTO($priceListItem),
        );
    }

    public function createPriceListItemUpdateResponseDTO(PriceListItem $priceListItem): PriceListItemUpdateResponseDTO
    {
        return new PriceListItemUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createPriceListItemDTO($priceListItem),
        );
    }

    public function createPriceListItemDeleteResponseDTO(): PriceListItemDeleteResponseDTO
    {
        return new PriceListItemDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createInventoryItemSummaryDTO(InventoryItem $inventoryItem): InventoryItemSummaryDTO
    {
        return new InventoryItemSummaryDTO(
            $inventoryItem->getId()->__toString(),
            $inventoryItem->getLabel(),
            $inventoryItem->getCode(),
            $inventoryItem->getOemCode(),
            $inventoryItem->getStockCount(),
        );
    }

    public function createInventoryItemHistoryDTO(InventoryItemHistory $history): InventoryItemHistoryDTO
    {
        $order = $history->getOrder();

        return new InventoryItemHistoryDTO(
            $history->getId()->__toString(),
            $history->getMovementType()->value,
            $history->getQuantity(),
            $history->getIsIncrement(),
            $history->getPrice(),
            $history->getVat(),
            $history->getPriceVat(),
            $history->getNote(),
            $order ? $this->createOrderSummaryDTO($order) : null,
            $history->getCreatedAt()->format(DateTimeInterface::ATOM),
            $this->createAuthUserDTO($history->getCreatedBy()),
        );
    }

    /**
     * @param InventoryItemHistory[] $historyItems
     *
     * @return InventoryItemHistoryDTO[]
     */
    public function createInventoryItemHistoryDTOs(array $historyItems): array
    {
        $dtoItems = [];

        foreach ($historyItems as $historyItem) {
            $dtoItems[] = $this->createInventoryItemHistoryDTO($historyItem);
        }

        return $dtoItems;
    }

    public function createInventoryItemDTO(InventoryItem $inventoryItem): InventoryItemDTO
    {
        $historyItems = $inventoryItem->getHistory()->slice(0, 20);
        $historyDTOs = $this->createInventoryItemHistoryDTOs($historyItems);

        return new InventoryItemDTO(
            $inventoryItem->getId()->__toString(),
            $inventoryItem->getLabel(),
            $inventoryItem->getDescription(),
            $inventoryItem->getCode(),
            $inventoryItem->getOemCode(),
            $inventoryItem->getPrice(),
            $inventoryItem->getVat(),
            $inventoryItem->getPriceVat(),
            $inventoryItem->getStockCount(),
            $inventoryItem->getCreatedAt()->format(DateTimeInterface::ATOM),
            $inventoryItem->getUpdatedAt()->format(DateTimeInterface::ATOM),
            $this->createAuthUserDTO($inventoryItem->getCreatedBy()),
            $inventoryItem->getUpdatedBy()->getId()->__toString(),
            $historyDTOs,
        );
    }

    /**
     * @param InventoryItem[] $inventoryItems
     *
     * @return InventoryItemDTO[]
     */
    public function createInventoryItemDTOs(array $inventoryItems): array
    {
        $dtoItems = [];

        foreach ($inventoryItems as $inventoryItem) {
            $dtoItems[] = $this->createInventoryItemDTO($inventoryItem);
        }

        return $dtoItems;
    }

    /**
     * @param InventoryItem[] $inventoryItems
     */
    public function createInventoryItemListResponseDTO(array $inventoryItems, int $pageCount): InventoryItemListResponseDTO
    {
        return new InventoryItemListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createInventoryItemDTOs($inventoryItems),
            $pageCount,
        );
    }

    public function createInventoryItemInfoResponseDTO(InventoryItem $inventoryItem): InventoryItemInfoResponseDTO
    {
        return new InventoryItemInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createInventoryItemDTO($inventoryItem),
        );
    }

    public function createInventoryItemCreateResponseDTO(InventoryItem $inventoryItem): InventoryItemCreateResponseDTO
    {
        return new InventoryItemCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createInventoryItemDTO($inventoryItem),
        );
    }

    public function createInventoryItemUpdateResponseDTO(InventoryItem $inventoryItem): InventoryItemUpdateResponseDTO
    {
        return new InventoryItemUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createInventoryItemDTO($inventoryItem),
        );
    }

    public function createInventoryItemDeleteResponseDTO(): InventoryItemDeleteResponseDTO
    {
        return new InventoryItemDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createInventoryItemStockInResponseDTO(InventoryItem $inventoryItem): InventoryItemStockInResponseDTO
    {
        return new InventoryItemStockInResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createInventoryItemDTO($inventoryItem),
        );
    }

    public function createInventoryItemStockOutResponseDTO(InventoryItem $inventoryItem): InventoryItemStockOutResponseDTO
    {
        return new InventoryItemStockOutResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createInventoryItemDTO($inventoryItem),
        );
    }

    public function createPriceListItemPublicDTO(PriceListItem $priceListItem): PriceListItemPublicDTO
    {
        return new PriceListItemPublicDTO(
            $priceListItem->getId()->__toString(),
            $priceListItem->getLabel(),
            $priceListItem->getDescription(),
            $priceListItem->getInvoiceLabel(),
            $priceListItem->getPrice(),
            $priceListItem->getVat(),
            $priceListItem->getPriceVat(),
        );
    }

    /**
     * @param PriceListItem[] $priceListItems
     */
    public function createPriceListItemPublicListResponseDTO(array $priceListItems): PriceListItemPublicListResponseDTO
    {
        $dtoItems = [];

        foreach ($priceListItems as $priceListItem) {
            $dtoItems[] = $this->createPriceListItemPublicDTO($priceListItem);
        }

        return new PriceListItemPublicListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $dtoItems,
        );
    }

    public function createCarDTO(Car $car): CarDTO
    {
        return new CarDTO(
            $car->getId()->__toString(),
            $car->getLabel(),
            $car->getIdent(),
            $car->getLicensePlate(),
            $car->getStatus()->value,
            $car->getCreatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    public function createCustomerCarDTO(CustomerCar $car): CustomerCarDTO
    {
        return new CustomerCarDTO(
            $car->getId()->__toString(),
            $car->getLicensePlate(),
            $car->getBrand()?->value,
            $car->getModel(),
            $car->getVin(),
            $car->getUser() ? $this->createOilServiceUserDTOWithoutCars($car->getUser()) : null,
            $car->getEngine() ? $this->createEngineSummaryDTO($car->getEngine()) : null,
            $car->getCreatedAt()->format(DateTimeInterface::ATOM),
            $car->getDkDatumPrvniRegistrace(),
            $car->getDkDatumPrvniRegistraceVCr(),
            $car->getDkCisloTypovehoSchvaleni(),
            $car->getDkHomologaceEs(),
            $car->getDkVozidloDruh(),
            $car->getDkVozidloDruh2(),
            $car->getDkKategorie(),
            $car->getDkTovarniZnacka(),
            $car->getDkTyp(),
            $car->getDkVarianta(),
            $car->getDkVerze(),
            $car->getDkVin(),
            $car->getDkObchodniOznaceni(),
            $car->getDkVozidloVyrobce(),
            $car->getDkMotorVyrobce(),
            $car->getDkMotorTyp(),
            $car->getDkMotorMaxVykon(),
            $car->getDkPalivo(),
            $car->getDkMotorZdvihObjem(),
            $car->getDkVozidloElektricke(),
            $car->getDkVozidloHybridni(),
            $car->getDkVozidloHybridniTrida(),
            $car->getDkEmiseEHKOSNEHSES(),
            $car->getDkEmisniUroven(),
            $car->getDkEmiseKSA(),
            $car->getDkEmiseCO2(),
            $car->getDkEmiseCO2Specificke(),
            $car->getDkEmiseSnizeniNedc(),
            $car->getDkEmiseSnizeniWltp(),
            $car->getDkSpotrebaMetodika(),
            $car->getDkSpotrebaNa100Km(),
            $car->getDkSpotreba(),
            $car->getDkSpotrebaEl(),
            $car->getDkDojezdZR(),
            $car->getDkVyrobceKaroserie(),
            $car->getDkKaroserieDruh(),
            $car->getDkKaroserieVyrobniCislo(),
            $car->getDkVozidloKaroserieBarva(),
            $car->getDkVozidloKaroserieBarvaDoplnkova(),
            $car->getDkVozidloKaroserieMist(),
            $car->getDkRozmery(),
            $car->getDkRozmeryRozvor(),
            $car->getDkRozchod(),
            $car->getDkHmotnostiProvozni(),
            $car->getDkHmotnostiPripPov(),
            $car->getDkHmotnostiPripPovN(),
            $car->getDkHmotnostiPripPovBrzdenePV(),
            $car->getDkHmotnostiPripPovNebrzdenePV(),
            $car->getDkHmotnostiPripPovJS(),
            $car->getDkHmotnostiTestWltp(),
            $car->getDkHmotnostUzitecneZatizeniPrumer(),
            $car->getDkVozidloSpojZarizNazev(),
            $car->getDkNapravyPocetDruh(),
            $car->getDkNapravyPneuRafky(),
            $car->getDkHlukStojiciOtacky(),
            $car->getDkHlukJizda(),
            $car->getDkNejvyssiRychlost(),
            $car->getDkPomerVykonHmotnost(),
            $car->getDkInovativniTechnologie(),
            $car->getDkStupenDokonceni(),
            $car->getDkFaktorOdchylkyDe(),
            $car->getDkFaktorVerifikaceVf(),
            $car->getDkVozidloUcel(),
            $car->getDkDalsiZaznamy(),
            $car->getDkAlternativniProvedeni(),
            $car->getDkCisloTp(),
            $car->getDkCisloOrv(),
            $car->getDkOrvZadrzeno(),
            $car->getDkOrvKeSkartaci(),
            $car->getDkOrvOdevzdano(),
            $car->getDkRzDruh(),
            $car->getDkRzJkVydana(),
            $car->getDkRzKeSkartaci(),
            $car->getDkRzOdevzdano(),
            $car->getDkRzZadrzena(),
            $car->getDkZarazeniVozidla(),
            $car->getDkPravidelnaTechnickaProhlidkaDo(),
            $car->getDkPredRegistraciProhlidkaDne(),
            $car->getDkPredSchvalenimProhlidkaDne(),
            $car->getDkEvidencniProhlidkaDne(),
            $car->getDkHistorickeVozidloProhlidkaDne(),
            $car->getDkStatusNazev(),
            $car->getDkPocetVlastniku(),
            $car->getDkPocetProvozovatelu(),
        );
    }

    public function createEngineSummaryDTO(CarDatabaseEngine $engine): EngineSummaryDTO
    {
        return new EngineSummaryDTO(
            $engine->getId()->__toString(),
            $engine->getManufacturer(),
            $engine->getModel(),
            $engine->getEngineCode(),
        );
    }

    public function createFilterSummaryDTO(CarDatabaseFilter $filter): FilterSummaryDTO
    {
        return new FilterSummaryDTO(
            $filter->getId()->__toString(),
            $filter->getFilterType()->value,
            $filter->getManufacturer(),
            $filter->getCode(),
            $filter->getOemCode(),
        );
    }

    public function createCustomerCarEngineFilterDTO(
        CarDatabaseEngineFilter $engineFilter,
        ?InventoryItem $inventoryItem,
    ): CustomerCarEngineFilterDTO {
        $inventoryItemDTO = $inventoryItem !== null ? $this->createInventoryItemSummaryDTO($inventoryItem) : null;

        return new CustomerCarEngineFilterDTO(
            $this->createFilterSummaryDTO($engineFilter->getFilter()),
            $inventoryItemDTO,
            $engineFilter->isPrimary(),
            $engineFilter->getSource(),
        );
    }

    /**
     * @param CustomerCar[] $cars
     *
     * @return CustomerCarDTO[]
     */
    public function createCustomerCarDTOs(array $cars): array
    {
        $items = [];

        foreach ($cars as $car) {
            $items[] = $this->createCustomerCarDTO($car);
        }

        return $items;
    }

    public function createCustomerCarHistoryDTO(CustomerCarHistory $history): CustomerCarHistoryDTO
    {
        return new CustomerCarHistoryDTO(
            $history->getId()->__toString(),
            $this->createOilServiceUserDTOWithoutCars($history->getUser()),
            $history->getAssignedAt()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @param CustomerCarHistory[] $history
     *
     * @return CustomerCarHistoryDTO[]
     */
    public function createCustomerCarHistoryDTOs(array $history): array
    {
        $items = [];

        foreach ($history as $item) {
            $items[] = $this->createCustomerCarHistoryDTO($item);
        }

        return $items;
    }

    /**
     * @param CustomerCarEngineFilterDTO[] $engineFilters
     */
    public function createCustomerCarDetailDTO(CustomerCar $car, array $engineFilters): CustomerCarDetailDTO
    {
        $history = $this->createCustomerCarHistoryDTOs($car->getHistory()->toArray());

        return new CustomerCarDetailDTO(
            $this->createCustomerCarDTO($car),
            $history,
            $engineFilters,
        );
    }

    public function createCustomerCarListResponseDTO(array $cars, int $pageCount): CustomerCarListResponseDTO
    {
        return new CustomerCarListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createCustomerCarDTOs($cars),
            $pageCount,
        );
    }

    /**
     * @param CustomerCarEngineFilterDTO[] $engineFilters
     */
    public function createCustomerCarInfoResponseDTO(CustomerCar $car, array $engineFilters): CustomerCarInfoResponseDTO
    {
        return new CustomerCarInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createCustomerCarDetailDTO($car, $engineFilters),
        );
    }

    public function createCustomerCarCreateResponseDTO(CustomerCar $car): CustomerCarCreateResponseDTO
    {
        return new CustomerCarCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createCustomerCarDTO($car),
        );
    }

    public function createCustomerCarUpdateResponseDTO(CustomerCar $car): CustomerCarUpdateResponseDTO
    {
        return new CustomerCarUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createCustomerCarDTO($car),
        );
    }

    public function createCustomerCarDeleteResponseDTO(): CustomerCarDeleteResponseDTO
    {
        return new CustomerCarDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createCustomerCarHistoryDeleteResponseDTO(): CustomerCarHistoryDeleteResponseDTO
    {
        return new CustomerCarHistoryDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createOrderCustomerCarResolveResponseDTO(CustomerCar $car): OrderCustomerCarResolveResponseDTO
    {
        return new OrderCustomerCarResolveResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createCustomerCarDTO($car),
        );
    }

    public function createOrderCustomerCarConflictResponseDTO(?CustomerCar $car): OrderCustomerCarConflictResponseDTO
    {
        return new OrderCustomerCarConflictResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $car !== null,
            $car !== null ? $this->createCustomerCarDTO($car) : null,
        );
    }

    /**
     * @param Car[] $cars
     */
    public function createCarListResponseDTO(array $cars, int $pageCount): CarListResponseDTO
    {
        $carDTOs = [];

        foreach ($cars as $car) {
            $carDTOs[] = $this->createCarDTO($car);
        }

        return new CarListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $carDTOs,
            $pageCount,
        );
    }

    public function createCarInfoResponseDTO(Car $car): CarInfoResponseDTO
    {
        return new CarInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createCarDTO($car),
        );
    }

    public function createCarCreateResponseDTO(Car $car): CarCreateResponseDTO
    {
        return new CarCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createCarDTO($car),
        );
    }

    public function createCarUpdateResponseDTO(Car $car): CarUpdateResponseDTO
    {
        return new CarUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createCarDTO($car),
        );
    }

    public function createCarDeleteResponseDTO(): CarDeleteResponseDTO
    {
        return new CarDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createChatMessageDTO(ChatMessage $message): ChatMessageDTO
    {
        return new ChatMessageDTO(
            $message->getRole()->value,
            $message->getContent(),
            $message->getCreatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @param ChatMessage[] $messages
     *
     * @return ChatMessageDTO[]
     */
    private function createChatMessageDTOs(array $messages): array
    {
        $items = [];

        foreach ($messages as $message) {
            $items[] = $this->createChatMessageDTO($message);
        }

        return $items;
    }

    /**
     * @param ChatMessage[] $messages
     */
    public function createChatSessionCreateResponseDTO(
        ChatSession $session,
        string $greeting,
        array $messages,
    ): ChatSessionCreateResponseDTO {
        $orderData = $this->resolveOrderIdentifiers($session->getOrder());

        return new ChatSessionCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $session->getId()->__toString(),
            $session->getFormattedIdent(),
            $session->getLanguage(),
            $greeting,
            $orderData['orderId'],
            $orderData['orderIdent'],
            $this->createChatMessageDTOs($messages),
        );
    }

    /**
     * @param ChatMessage[] $messages
     */
    public function createChatMessageResponseDTO(
        ChatSession $session,
        ChatMessage $assistantMessage,
        array $messages,
    ): ChatMessageResponseDTO {
        $orderData = $this->resolveOrderIdentifiers($session->getOrder());

        return new ChatMessageResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $session->getId()->__toString(),
            $session->getFormattedIdent(),
            $session->getStatus()->value,
            $orderData['orderId'],
            $orderData['orderIdent'],
            $assistantMessage->getContent(),
            $this->createChatMessageDTOs($messages),
        );
    }

    /**
     * @param ChatMessage[] $messages
     */
    public function createChatMessageListResponseDTO(ChatSession $session, array $messages): ChatMessageListResponseDTO
    {
        $orderData = $this->resolveOrderIdentifiers($session->getOrder());

        return new ChatMessageListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $session->getId()->__toString(),
            $session->getFormattedIdent(),
            $session->getStatus()->value,
            $session->getLanguage(),
            $orderData['orderId'],
            $orderData['orderIdent'],
            $this->createChatMessageDTOs($messages),
        );
    }

    public function createChatDefaultMessageResponseDTO(string $language, string $message): ChatDefaultMessageResponseDTO
    {
        return new ChatDefaultMessageResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $language,
            $message,
        );
    }

    public function createChatSessionCompleteResponseDTO(): ChatSessionCompleteResponseDTO
    {
        return new ChatSessionCompleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    /**
     * @return array{orderId: ?string, orderIdent: ?string}
     */
    private function resolveOrderIdentifiers(?Order $order): array
    {
        return [
            'orderId' => $order?->getId()?->__toString(),
            'orderIdent' => $order?->getFormattedIdent(),
        ];
    }

    public function createChatKnowledgeItemDTO(ChatKnowledgeItem $item): ChatKnowledgeItemDTO
    {
        return new ChatKnowledgeItemDTO(
            $item->getId()->__toString(),
            $item->getName(),
            $item->getContent(),
            $item->getType()->value,
            $item->getLanguage(),
            $item->getIsActive(),
            $item->getCreatedAt()->format(DateTimeInterface::ATOM),
            $item->getUpdatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @param ChatKnowledgeItem[] $items
     *
     * @return ChatKnowledgeItemDTO[]
     */
    private function createChatKnowledgeItemDTOs(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = $this->createChatKnowledgeItemDTO($item);
        }

        return $result;
    }

    public function createChatKnowledgeItemCreateResponseDTO(ChatKnowledgeItem $item): ChatKnowledgeItemCreateResponseDTO
    {
        return new ChatKnowledgeItemCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createChatKnowledgeItemDTO($item),
        );
    }

    public function createChatKnowledgeItemUpdateResponseDTO(ChatKnowledgeItem $item): ChatKnowledgeItemUpdateResponseDTO
    {
        return new ChatKnowledgeItemUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createChatKnowledgeItemDTO($item),
        );
    }

    public function createChatKnowledgeItemDeleteResponseDTO(): ChatKnowledgeItemDeleteResponseDTO
    {
        return new ChatKnowledgeItemDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    /**
     * @param ChatKnowledgeItem[] $items
     */
    public function createChatKnowledgeItemListResponseDTO(array $items): ChatKnowledgeItemListResponseDTO
    {
        return new ChatKnowledgeItemListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createChatKnowledgeItemDTOs($items),
        );
    }

    public function createChatKnowledgeItemInfoResponseDTO(ChatKnowledgeItem $item): ChatKnowledgeItemInfoResponseDTO
    {
        return new ChatKnowledgeItemInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createChatKnowledgeItemDTO($item),
        );
    }

    public function createChatSessionLightDTO(ChatSession $session): ChatSessionLightDTO
    {
        $orderData = $this->resolveOrderIdentifiers($session->getOrder());

        return new ChatSessionLightDTO(
            $session->getId()->__toString(),
            $session->getFormattedIdent(),
            $session->getStatus()->value,
            $session->getLanguage(),
            $session->getCreatedAt()->format(DateTimeInterface::ATOM),
            $orderData['orderId'],
            $orderData['orderIdent'],
        );
    }

    public function createChatSessionDTO(ChatSession $session): ChatSessionDTO
    {
        $orderData = $this->resolveOrderIdentifiers($session->getOrder());

        return new ChatSessionDTO(
            $session->getId()->__toString(),
            $session->getFormattedIdent(),
            $session->getStatus()->value,
            $session->getLanguage(),
            $session->getCreatedAt()->format(DateTimeInterface::ATOM),
            $session->getUpdatedAt()->format(DateTimeInterface::ATOM),
            $session->getClosedAt()?->format(DateTimeInterface::ATOM),
            $orderData['orderId'],
            $orderData['orderIdent'],
        );
    }

    public function createChatSessionDetailDTO(ChatSession $session): ChatSessionDetailDTO
    {
        $messages = $session->getMessages()->toArray();
        $orderData = $this->resolveOrderIdentifiers($session->getOrder());

        return new ChatSessionDetailDTO(
            $session->getId()->__toString(),
            $session->getFormattedIdent(),
            $session->getStatus()->value,
            $session->getLanguage(),
            $session->getCreatedAt()->format(DateTimeInterface::ATOM),
            $session->getUpdatedAt()->format(DateTimeInterface::ATOM),
            $session->getClosedAt()?->format(DateTimeInterface::ATOM),
            $orderData['orderId'],
            $orderData['orderIdent'],
            $this->createChatMessageDTOs($messages),
        );
    }

    public function createChatUserRequestDTO(ChatUserRequest $request): ChatUserRequestDTO
    {
        $session = $request->getSession();
        $sessionDTO = $session !== null
            ? $this->createChatSessionLightDTO($session)
            : new ChatSessionLightDTO('', '', 'active', '', '', null, null);

        return new ChatUserRequestDTO(
            $request->getId()->__toString(),
            $request->getFormattedIdent(),
            $request->getStatus()->value,
            $request->getContent(),
            $request->getCreatedAt()->format(DateTimeInterface::ATOM),
            $request->getResolvedAt()?->format(DateTimeInterface::ATOM),
            $request->getIsResolved(),
            $request->getNote(),
            $sessionDTO,
        );
    }

    public function createChatUserRequestDetailDTO(ChatUserRequest $request): ChatUserRequestDetailDTO
    {
        $session = $request->getSession();
        $sessionDTO = $session !== null
            ? $this->createChatSessionDetailDTO($session)
            : null;

        return new ChatUserRequestDetailDTO(
            $request->getId()->__toString(),
            $request->getFormattedIdent(),
            $request->getStatus()->value,
            $request->getContent(),
            $request->getCreatedAt()->format(DateTimeInterface::ATOM),
            $request->getResolvedAt()?->format(DateTimeInterface::ATOM),
            $request->getIsResolved(),
            $request->getNote(),
            $sessionDTO,
        );
    }

    /**
     * @param ChatUserRequest[] $requests
     *
     * @return ChatUserRequestDTO[]
     */
    private function createChatUserRequestDTOs(array $requests): array
    {
        $items = [];

        foreach ($requests as $request) {
            $items[] = $this->createChatUserRequestDTO($request);
        }

        return $items;
    }

    /**
     * @param ChatUserRequest[] $requests
     */
    public function createChatUserRequestListResponseDTO(array $requests, int $pageCount): ChatUserRequestListResponseDTO
    {
        return new ChatUserRequestListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createChatUserRequestDTOs($requests),
            $pageCount,
        );
    }

    public function createChatUserRequestInfoResponseDTO(ChatUserRequest $request): ChatUserRequestInfoResponseDTO
    {
        return new ChatUserRequestInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createChatUserRequestDetailDTO($request),
        );
    }

    public function createChatUserRequestUpdateResponseDTO(ChatUserRequest $request): ChatUserRequestUpdateResponseDTO
    {
        return new ChatUserRequestUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createChatUserRequestDTO($request),
        );
    }

    public function createChatUserRequestResolveResponseDTO(ChatUserRequest $request): ChatUserRequestResolveResponseDTO
    {
        return new ChatUserRequestResolveResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createChatUserRequestDTO($request),
        );
    }

    public function createChatUserRequestDeleteResponseDTO(): ChatUserRequestDeleteResponseDTO
    {
        return new ChatUserRequestDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    /**
     * @param ChatSession[] $sessions
     */
    public function createChatSessionListResponseDTO(array $sessions, int $pageCount): ChatSessionListResponseDTO
    {
        $items = [];

        foreach ($sessions as $session) {
            $items[] = $this->createChatSessionDTO($session);
        }

        return new ChatSessionListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $items,
            $pageCount,
        );
    }

    public function createChatSessionInfoResponseDTO(ChatSession $session): ChatSessionInfoResponseDTO
    {
        return new ChatSessionInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createChatSessionDetailDTO($session),
        );
    }
}
