<?php

declare(strict_types=1);

namespace App\Modules\OilService\Factory;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Domain\DTOValueResolver;
use App\Domain\Exception\InvalidArgumentException;
use App\Modules\OilService\DTO\OrderCreateResponseDTO;
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
use App\Modules\OilService\DTO\CarDTO;
use App\Modules\OilService\DTO\CarCreateResponseDTO;
use App\Modules\OilService\DTO\CarUpdateResponseDTO;
use App\Modules\OilService\DTO\CarDeleteResponseDTO;
use App\Modules\OilService\DTO\CarInfoResponseDTO;
use App\Modules\OilService\DTO\CarListResponseDTO;
use App\Modules\Users\DTO\UserDTO;
use App\OilService\DBAL\Entity\Car;
use App\OilService\DBAL\Entity\InventoryItem;
use App\OilService\DBAL\Entity\InventoryItemHistory;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Entity\OrderInventoryItem;
use App\OilService\DBAL\Entity\PriceListItem;
use App\OilService\DBAL\Entity\Route;
use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Entity\User;
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
        return new OilServiceUserDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getFullName(),
            $user->getCreatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    public function createOilServiceUserListDTO(User $user): OilServiceUserListDTO
    {
        return new OilServiceUserListDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getFullName(),
            $user->getCreatedAt()->format(DateTimeInterface::ATOM),
            $user->getOrders()->count(),
        );
    }

    public function createOilServiceUserWithOrdersDTO(User $user): OilServiceUserWithOrdersDTO
    {
        /** @var OrderDTO[] $orders */
        $orders = $this->createOrderDTOCollection($user->getOrders()->toArray())->toArray();

        return new OilServiceUserWithOrdersDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getFullName(),
            $user->getCreatedAt()->format(DateTimeInterface::ATOM),
            $orders,
        );
    }

    public function createOrderDTO(Order $order): OrderDTO
    {
        $route = $order->getRoute();
        $materials = $this->createOrderStorageContainerMaterialDTOs($order);
        $inventoryItems = $this->createOrderInventoryItemDTOs($order);
        $priceListItems = $this->createPriceListItemDTOs($order->getPriceListItems()->toArray());

        return new OrderDTO(
            $order->getId()->__toString(),
            $order->getFormattedIdent(),
            $order->getFullName(),
            $order->getPhone(),
            $order->getEmail(),
            $order->getCarModel(),
            $order->getLicensePlate(),
            $order->getAddress(),
            $order->getNote(),
            $order->getIsCompany(),
            $order->getCompanyName(),
            $order->getCompanyIdentificationNumber(),
            $order->getCompanyTaxId(),
            $order->getCompanyAddress(),
            $order->getStatus()->value,
            $order->getRealizationTimeSlot()->value,
            $order->getRealizationDate()->format('Y-m-d'),
            $order->getCreatedAt()->format(DateTimeInterface::ATOM),
            $this->createOilServiceUserDTO($order->getUser()),
            $route ? $this->createRouteDTO($route) : null,
            $materials,
            $inventoryItems,
            $priceListItems,
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

        foreach ($route->getOrders() as $order) {
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

    private function createOrderSummaryDTO(Order $order): OrderSummaryDTO
    {
        return new OrderSummaryDTO(
            $order->getId()->__toString(),
            $order->getFormattedIdent(),
            $order->getFullName(),
            $order->getStatus()->value,
            $order->getRealizationDate()->format('Y-m-d'),
        );
    }

    private function createAuthUserDTO(AuthUser $user): UserDTO
    {
        return new UserDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getFullName(),
            $user->getIsActive(),
            $user->getIsAdmin(),
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
            $history->getCreatedBy()->getId()->__toString(),
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
            $inventoryItem->getPrice(),
            $inventoryItem->getVat(),
            $inventoryItem->getPriceVat(),
            $inventoryItem->getStockCount(),
            $inventoryItem->getCreatedAt()->format(DateTimeInterface::ATOM),
            $inventoryItem->getUpdatedAt()->format(DateTimeInterface::ATOM),
            $inventoryItem->getCreatedBy()->getId()->__toString(),
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
}
