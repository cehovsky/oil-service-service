<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Factory;

use App\Domain\DTOValueResolver;
use App\Modules\Warehouse\DTO\RouteSummaryDTO;
use App\Modules\Warehouse\DTO\StorageContainerActualLocationDTO;
use App\Modules\Warehouse\DTO\StorageContainerCreateResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerDeleteResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerDTO;
use App\Modules\Warehouse\DTO\StorageContainerInfoResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerListResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationBasicDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationCreateResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationDeleteResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationInfoResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationListResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationUpdateResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerSummaryDTO;
use App\Modules\Warehouse\DTO\StorageContainerUpdateResponseDTO;
use App\Modules\Warehouse\DTO\WarehouseCreateResponseDTO;
use App\Modules\Warehouse\DTO\WarehouseCurrentLocationDTO;
use App\Modules\Warehouse\DTO\WarehouseDeleteResponseDTO;
use App\Modules\Warehouse\DTO\WarehouseDetailDTO;
use App\Modules\Warehouse\DTO\WarehouseDTO;
use App\Modules\Warehouse\DTO\WarehouseInfoResponseDTO;
use App\Modules\Warehouse\DTO\WarehouseListResponseDTO;
use App\Modules\Warehouse\DTO\WarehouseSummaryDTO;
use App\Modules\Warehouse\DTO\WarehouseUpdateResponseDTO;
use App\Modules\Warehouse\DTO\WasteMaterialCreateResponseDTO;
use App\Modules\Warehouse\DTO\WasteMaterialDeleteResponseDTO;
use App\Modules\Warehouse\DTO\WasteMaterialDetailDTO;
use App\Modules\Warehouse\DTO\WasteMaterialDTO;
use App\Modules\Warehouse\DTO\WasteMaterialInfoResponseDTO;
use App\Modules\Warehouse\DTO\WasteMaterialListResponseDTO;
use App\Modules\Warehouse\DTO\WasteMaterialSummaryDTO;
use App\Modules\Warehouse\DTO\WasteMaterialUpdateResponseDTO;
use App\Modules\Warehouse\DTO\RecyclingCreateResponseDTO;
use App\Modules\Warehouse\DTO\RecyclingDeleteResponseDTO;
use App\Modules\Warehouse\DTO\RecyclingDTO;
use App\Modules\Warehouse\DTO\RecyclingInfoResponseDTO;
use App\Modules\Warehouse\DTO\RecyclingListResponseDTO;
use App\Modules\Warehouse\DTO\RecyclingRecycleResponseDTO;
use App\Modules\Warehouse\DTO\RecyclingSummaryDTO;
use App\Modules\Warehouse\DTO\RecyclingUpdateResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialCreateResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialDeleteResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialHistoryDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialHistoryDeleteResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialHistoryListResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialSummaryDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialInfoResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialListResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialMoveResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialUpdateResponseDTO;
use App\Modules\OilService\DTO\OrderSummaryDTO;
use App\Modules\Users\DTO\UserDTO;
use App\Auth\DBAL\Entity\User;
use App\OilService\DBAL\Entity\Route;
use App\Warehouse\DBAL\Entity\Recycling;
use App\Warehouse\DBAL\Entity\StorageContainer;
use App\Warehouse\DBAL\Entity\StorageContainerLocation;
use App\Warehouse\DBAL\Entity\StorageContainerMaterial;
use App\Warehouse\DBAL\Entity\StorageContainerMaterialHistory;
use App\Warehouse\DBAL\Entity\WasteMaterial;
use App\Warehouse\DBAL\Entity\Warehouse;
use DateTimeInterface;

class DTOFactory
{
    public function createWarehouseDTO(Warehouse $warehouse): WarehouseDTO
    {
        return new WarehouseDTO(
            $warehouse->getId()->__toString(),
            $warehouse->getLabel(),
            $warehouse->getShortLabel(),
            $warehouse->getAddress(),
            $warehouse->getIsActive(),
            $warehouse->getCreatedAt()->format(DateTimeInterface::ATOM),
            $warehouse->getUpdatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @param StorageContainerLocation[] $currentLocations
     */
    public function createWarehouseDetailDTO(Warehouse $warehouse, array $currentLocations): WarehouseDetailDTO
    {
        $locationDTOs = [];

        foreach ($currentLocations as $location) {
            $locationDTOs[] = $this->createWarehouseCurrentLocationDTO($location);
        }

        return new WarehouseDetailDTO(
            $this->createWarehouseDTO($warehouse),
            $locationDTOs,
        );
    }

    public function createWarehouseCreateResponseDTO(Warehouse $warehouse): WarehouseCreateResponseDTO
    {
        return new WarehouseCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createWarehouseDTO($warehouse),
        );
    }

    public function createWarehouseUpdateResponseDTO(Warehouse $warehouse): WarehouseUpdateResponseDTO
    {
        return new WarehouseUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createWarehouseDTO($warehouse),
        );
    }

    /**
     * @param array<StorageContainerLocation> $currentLocations
     */
    public function createWarehouseInfoResponseDTO(Warehouse $warehouse, array $currentLocations): WarehouseInfoResponseDTO
    {
        return new WarehouseInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createWarehouseDetailDTO($warehouse, $currentLocations),
        );
    }

    /**
     * @param Warehouse[] $warehouses
     */
    public function createWarehouseListResponseDTO(array $warehouses, int $pageCount): WarehouseListResponseDTO
    {
        $warehouseDTOs = [];

        foreach ($warehouses as $warehouse) {
            $warehouseDTOs[] = $this->createWarehouseDTO($warehouse);
        }

        return new WarehouseListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $warehouseDTOs,
            $pageCount,
        );
    }

    public function createWarehouseDeleteResponseDTO(): WarehouseDeleteResponseDTO
    {
        return new WarehouseDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createWasteMaterialDTO(WasteMaterial $wasteMaterial): WasteMaterialDTO
    {
        return new WasteMaterialDTO(
            $wasteMaterial->getId()->__toString(),
            $wasteMaterial->getCode(),
            $wasteMaterial->getLabel(),
            $wasteMaterial->getShortLabel(),
            $wasteMaterial->getIsActive(),
            $wasteMaterial->getVolumeUnit()->value,
            $wasteMaterial->getCatalogDescription(),
            $wasteMaterial->getCreatedAt()->format(DateTimeInterface::ATOM),
            $wasteMaterial->getUpdatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    public function createWasteMaterialDetailDTO(WasteMaterial $wasteMaterial): WasteMaterialDetailDTO
    {
        $storageContainers = [];

        foreach ($wasteMaterial->getPreferredStorageContainers() as $storageContainer) {
            $storageContainers[] = $this->createStorageContainerSummaryDTO($storageContainer);
        }

        return new WasteMaterialDetailDTO(
            $this->createWasteMaterialDTO($wasteMaterial),
            $storageContainers,
        );
    }

    /**
     * @param WasteMaterial[] $wasteMaterials
     */
    public function createWasteMaterialListResponseDTO(array $wasteMaterials, int $pageCount): WasteMaterialListResponseDTO
    {
        $wasteMaterialDTOs = [];

        foreach ($wasteMaterials as $wasteMaterial) {
            $wasteMaterialDTOs[] = $this->createWasteMaterialDTO($wasteMaterial);
        }

        return new WasteMaterialListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $wasteMaterialDTOs,
            $pageCount,
        );
    }

    public function createWasteMaterialCreateResponseDTO(WasteMaterial $wasteMaterial): WasteMaterialCreateResponseDTO
    {
        return new WasteMaterialCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createWasteMaterialDTO($wasteMaterial),
        );
    }

    public function createWasteMaterialUpdateResponseDTO(WasteMaterial $wasteMaterial): WasteMaterialUpdateResponseDTO
    {
        return new WasteMaterialUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createWasteMaterialDTO($wasteMaterial),
        );
    }

    public function createWasteMaterialInfoResponseDTO(WasteMaterial $wasteMaterial): WasteMaterialInfoResponseDTO
    {
        return new WasteMaterialInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createWasteMaterialDetailDTO($wasteMaterial),
        );
    }

    public function createWasteMaterialDeleteResponseDTO(): WasteMaterialDeleteResponseDTO
    {
        return new WasteMaterialDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    /**
     * @param StorageContainerMaterial[] $currentMaterials
     */
    public function createStorageContainerDTO(
        StorageContainer $storageContainer,
        ?StorageContainerLocation $actualLocation = null,
        array $currentMaterials = [],
    ): StorageContainerDTO {
        $preferredWasteMaterials = [];

        foreach ($storageContainer->getPreferredWasteMaterials() as $preferredWasteMaterial) {
            $preferredWasteMaterials[] = $this->createWasteMaterialSummaryDTO($preferredWasteMaterial);
        }

        $actualLocationDTO = $actualLocation !== null
            ? $this->createStorageContainerActualLocationDTO($actualLocation)
            : null;

        $currentContent = [];

        foreach ($currentMaterials as $material) {
            $currentContent[] = $this->createStorageContainerMaterialSummaryDTO($material);
        }

        return new StorageContainerDTO(
            $storageContainer->getId()->__toString(),
            $storageContainer->getCode(),
            $storageContainer->getDescription(),
            $storageContainer->getIsActive(),
            $storageContainer->getType()->value,
            $storageContainer->getCapacity(),
            $storageContainer->getVolumeUnit()->value,
            $storageContainer->getCreatedAt()->format(DateTimeInterface::ATOM),
            $storageContainer->getUpdatedAt()->format(DateTimeInterface::ATOM),
            $preferredWasteMaterials,
            $actualLocationDTO,
            $currentContent,
        );
    }

    /**
     * @param StorageContainer[] $storageContainers
     * @param array<string, StorageContainerLocation> $actualLocations
     * @param array<string, StorageContainerMaterial[]> $currentMaterials
     */
    public function createStorageContainerListResponseDTO(
        array $storageContainers,
        array $actualLocations,
        int $pageCount,
        array $currentMaterials = []
    ): StorageContainerListResponseDTO {
        $storageContainerDTOs = [];

        foreach ($storageContainers as $storageContainer) {
            $actualLocation = $actualLocations[$storageContainer->getId()->__toString()] ?? null;
            $materials = $currentMaterials[$storageContainer->getId()->__toString()] ?? [];
            $storageContainerDTOs[] = $this->createStorageContainerDTO($storageContainer, $actualLocation, $materials);
        }

        return new StorageContainerListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $storageContainerDTOs,
            $pageCount,
        );
    }

    /**
     * @param StorageContainerMaterial[] $currentMaterials
     */
    public function createStorageContainerCreateResponseDTO(
        StorageContainer $storageContainer,
        ?StorageContainerLocation $actualLocation,
        array $currentMaterials = []
    ): StorageContainerCreateResponseDTO {
        return new StorageContainerCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createStorageContainerDTO($storageContainer, $actualLocation, $currentMaterials),
        );
    }

    /**
     * @param StorageContainerMaterial[] $currentMaterials
     */
    public function createStorageContainerUpdateResponseDTO(
        StorageContainer $storageContainer,
        ?StorageContainerLocation $actualLocation,
        array $currentMaterials = []
    ): StorageContainerUpdateResponseDTO {
        return new StorageContainerUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createStorageContainerDTO($storageContainer, $actualLocation, $currentMaterials),
        );
    }

    /**
     * @param StorageContainerMaterial[] $currentMaterials
     */
    public function createStorageContainerInfoResponseDTO(
        StorageContainer $storageContainer,
        ?StorageContainerLocation $actualLocation,
        array $currentMaterials = []
    ): StorageContainerInfoResponseDTO {
        return new StorageContainerInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createStorageContainerDTO($storageContainer, $actualLocation, $currentMaterials),
        );
    }

    public function createStorageContainerDeleteResponseDTO(): StorageContainerDeleteResponseDTO
    {
        return new StorageContainerDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createStorageContainerLocationDTO(StorageContainerLocation $storageContainerLocation): StorageContainerLocationDTO
    {
        $warehouse = $storageContainerLocation->getWarehouse();
        $route = $storageContainerLocation->getRoute();

        return new StorageContainerLocationDTO(
            $storageContainerLocation->getId()->__toString(),
            $this->createStorageContainerSummaryDTO($storageContainerLocation->getStorageContainer()),
            $warehouse ? $this->createWarehouseSummaryDTO($warehouse) : null,
            $route ? $this->createRouteSummaryDTO($route) : null,
            $storageContainerLocation->getMovedAt()->format(DateTimeInterface::ATOM),
            $storageContainerLocation->getCreatedAt()->format(DateTimeInterface::ATOM),
            $storageContainerLocation->getUpdatedAt()->format(DateTimeInterface::ATOM),
        );
    }

    /**
     * @param StorageContainerLocation[] $storageContainerLocations
     */
    public function createStorageContainerLocationListResponseDTO(
        array $storageContainerLocations,
        int $pageCount
    ): StorageContainerLocationListResponseDTO {
        $dtos = [];

        foreach ($storageContainerLocations as $location) {
            $dtos[] = $this->createStorageContainerLocationDTO($location);
        }

        return new StorageContainerLocationListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $dtos,
            $pageCount,
        );
    }

    public function createStorageContainerLocationCreateResponseDTO(StorageContainerLocation $storageContainerLocation): StorageContainerLocationCreateResponseDTO
    {
        return new StorageContainerLocationCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createStorageContainerLocationDTO($storageContainerLocation),
        );
    }

    public function createStorageContainerLocationUpdateResponseDTO(StorageContainerLocation $storageContainerLocation): StorageContainerLocationUpdateResponseDTO
    {
        return new StorageContainerLocationUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createStorageContainerLocationDTO($storageContainerLocation),
        );
    }

    public function createStorageContainerLocationInfoResponseDTO(StorageContainerLocation $storageContainerLocation): StorageContainerLocationInfoResponseDTO
    {
        return new StorageContainerLocationInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createStorageContainerLocationDTO($storageContainerLocation),
        );
    }

    public function createStorageContainerLocationDeleteResponseDTO(): StorageContainerLocationDeleteResponseDTO
    {
        return new StorageContainerLocationDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createStorageContainerMaterialDTO(StorageContainerMaterial $storageContainerMaterial): StorageContainerMaterialDTO
    {
        $warehouse = $storageContainerMaterial->getWarehouse();
        $route = $storageContainerMaterial->getRoute();
        $recycling = $storageContainerMaterial->getRecycling();
        $order = $storageContainerMaterial->getOrder();

        $orderSummary = null;

        if ($order !== null) {
            $orderSummary = new OrderSummaryDTO(
                $order->getId()->__toString(),
                $order->getFormattedIdent(),
                $order->getFullName(),
                $order->getStatus()->value,
                $order->getRealizationDate()->format('Y-m-d'),
            );
        }

        $historyDTOs = [];

        foreach ($storageContainerMaterial->getHistory() as $history) {
            $historyDTOs[] = $this->createStorageContainerMaterialHistoryDTO($history);
        }

        return new StorageContainerMaterialDTO(
            $storageContainerMaterial->getId()->__toString(),
            $this->createStorageContainerSummaryDTO($storageContainerMaterial->getStorageContainer()),
            $this->createWasteMaterialSummaryDTO($storageContainerMaterial->getWasteMaterial()),
            $warehouse ? $this->createWarehouseSummaryDTO($warehouse) : null,
            $route ? $this->createRouteSummaryDTO($route) : null,
            $recycling ? $this->createRecyclingSummaryDTO($recycling) : null,
            $orderSummary,
            $storageContainerMaterial->getVolume(),
            $storageContainerMaterial->getIsRecycled(),
            $storageContainerMaterial->getCreatedAt()->format(DateTimeInterface::ATOM),
            $storageContainerMaterial->getUpdatedAt()->format(DateTimeInterface::ATOM),
            $historyDTOs,
        );
    }

    /**
     * @param StorageContainerMaterial[] $storageContainerMaterials
     */
    public function createStorageContainerMaterialListResponseDTO(
        array $storageContainerMaterials,
        int $pageCount
    ): StorageContainerMaterialListResponseDTO {
        $dtos = [];

        foreach ($storageContainerMaterials as $material) {
            $dtos[] = $this->createStorageContainerMaterialDTO($material);
        }

        return new StorageContainerMaterialListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $dtos,
            $pageCount,
        );
    }

    public function createStorageContainerMaterialCreateResponseDTO(StorageContainerMaterial $storageContainerMaterial): StorageContainerMaterialCreateResponseDTO
    {
        return new StorageContainerMaterialCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createStorageContainerMaterialDTO($storageContainerMaterial),
        );
    }

    public function createStorageContainerMaterialUpdateResponseDTO(StorageContainerMaterial $storageContainerMaterial): StorageContainerMaterialUpdateResponseDTO
    {
        return new StorageContainerMaterialUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createStorageContainerMaterialDTO($storageContainerMaterial),
        );
    }

    /**
     * @param StorageContainerMaterial[] $storageContainerMaterials
     */
    public function createStorageContainerMaterialMoveResponseDTO(array $storageContainerMaterials): StorageContainerMaterialMoveResponseDTO
    {
        $dtos = [];

        foreach ($storageContainerMaterials as $material) {
            $dtos[] = $this->createStorageContainerMaterialDTO($material);
        }

        return new StorageContainerMaterialMoveResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $dtos,
        );
    }

    public function createStorageContainerMaterialInfoResponseDTO(StorageContainerMaterial $storageContainerMaterial): StorageContainerMaterialInfoResponseDTO
    {
        return new StorageContainerMaterialInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createStorageContainerMaterialDTO($storageContainerMaterial),
        );
    }

    public function createStorageContainerMaterialDeleteResponseDTO(): StorageContainerMaterialDeleteResponseDTO
    {
        return new StorageContainerMaterialDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    /**
     * @param StorageContainerMaterialHistory[] $history
     */
    public function createStorageContainerMaterialHistoryListResponseDTO(
        array $history,
        int $pageCount
    ): StorageContainerMaterialHistoryListResponseDTO {
        $historyDTOs = [];

        foreach ($history as $historyItem) {
            $historyDTOs[] = $this->createStorageContainerMaterialHistoryDTO($historyItem);
        }

        return new StorageContainerMaterialHistoryListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $historyDTOs,
            $pageCount,
        );
    }

    public function createStorageContainerMaterialHistoryDeleteResponseDTO(): StorageContainerMaterialHistoryDeleteResponseDTO
    {
        return new StorageContainerMaterialHistoryDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    /**
     * @param StorageContainerMaterial[]|null $storageContainerMaterials
     */
    public function createRecyclingDTO(
        Recycling $recycling,
        ?array $storageContainerMaterials = null,
    ): RecyclingDTO {
        $storageContainerDTOs = [];

        foreach ($recycling->getStorageContainers() as $storageContainer) {
            $storageContainerDTOs[] = $this->createStorageContainerSummaryDTO($storageContainer);
        }

        $materials = $storageContainerMaterials ?? $recycling->getStorageContainerMaterials()->toArray();

        $materialDTOs = [];

        foreach ($materials as $material) {
            $materialDTOs[] = $this->createStorageContainerMaterialDTO($material);
        }

        return new RecyclingDTO(
            $recycling->getId()->__toString(),
            $recycling->getRecycledAt()?->format('Y-m-d'),
            $recycling->getRecycledBy()?->getId()->__toString(),
            $recycling->getCreatedAt()->format(DateTimeInterface::ATOM),
            $recycling->getUpdatedAt()->format(DateTimeInterface::ATOM),
            $storageContainerDTOs,
            $materialDTOs,
        );
    }

    /**
     * @param Recycling[] $recyclings
     * @param array<string, StorageContainerMaterial[]> $storageContainerMaterialsByRecyclingId
     */
    public function createRecyclingListResponseDTO(
        array $recyclings,
        int $pageCount,
        array $storageContainerMaterialsByRecyclingId = [],
    ): RecyclingListResponseDTO {
        $dtos = [];

        foreach ($recyclings as $recycling) {
            $materials = $storageContainerMaterialsByRecyclingId[$recycling->getId()->__toString()] ?? null;
            $dtos[] = $this->createRecyclingDTO($recycling, $materials);
        }

        return new RecyclingListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $dtos,
            $pageCount,
        );
    }

    /**
     * @param StorageContainerMaterial[]|null $storageContainerMaterials
     */
    public function createRecyclingCreateResponseDTO(Recycling $recycling, ?array $storageContainerMaterials = null): RecyclingCreateResponseDTO
    {
        return new RecyclingCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createRecyclingDTO($recycling, $storageContainerMaterials),
        );
    }

    /**
     * @param StorageContainerMaterial[]|null $storageContainerMaterials
     */
    public function createRecyclingUpdateResponseDTO(Recycling $recycling, ?array $storageContainerMaterials = null): RecyclingUpdateResponseDTO
    {
        return new RecyclingUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createRecyclingDTO($recycling, $storageContainerMaterials),
        );
    }

    /**
     * @param StorageContainerMaterial[]|null $storageContainerMaterials
     */
    public function createRecyclingInfoResponseDTO(Recycling $recycling, ?array $storageContainerMaterials = null): RecyclingInfoResponseDTO
    {
        return new RecyclingInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createRecyclingDTO($recycling, $storageContainerMaterials),
        );
    }

    public function createRecyclingDeleteResponseDTO(): RecyclingDeleteResponseDTO
    {
        return new RecyclingDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    /**
     * @param StorageContainerMaterial[]|null $storageContainerMaterials
     */
    public function createRecyclingRecycleResponseDTO(Recycling $recycling, ?array $storageContainerMaterials = null): RecyclingRecycleResponseDTO
    {
        return new RecyclingRecycleResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createRecyclingDTO($recycling, $storageContainerMaterials),
        );
    }

    private function createStorageContainerMaterialHistoryDTO(StorageContainerMaterialHistory $history): StorageContainerMaterialHistoryDTO
    {
        return new StorageContainerMaterialHistoryDTO(
            $history->getId()->__toString(),
            $this->createStorageContainerMaterialSummaryDTO($history->getStorageContainerMaterial()),
            $this->createStorageContainerSummaryDTO($history->getStorageContainer()),
            $history->getCreatedAt()->format(DateTimeInterface::ATOM),
            $this->createUserDTO($history->getCreatedBy()),
        );
    }

    private function createStorageContainerMaterialSummaryDTO(StorageContainerMaterial $storageContainerMaterial): StorageContainerMaterialSummaryDTO
    {
        return new StorageContainerMaterialSummaryDTO(
            $storageContainerMaterial->getId()->__toString(),
            $this->createWasteMaterialSummaryDTO($storageContainerMaterial->getWasteMaterial()),
            $storageContainerMaterial->getVolume(),
            $storageContainerMaterial->getIsRecycled(),
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

    private function createStorageContainerActualLocationDTO(StorageContainerLocation $location): StorageContainerActualLocationDTO
    {
        $warehouse = $location->getWarehouse();
        $route = $location->getRoute();

        return new StorageContainerActualLocationDTO(
            $location->getId()->__toString(),
            $location->getMovedAt()->format(DateTimeInterface::ATOM),
            $warehouse !== null ? 'warehouse' : 'route',
            $warehouse ? $this->createWarehouseSummaryDTO($warehouse) : null,
            $route ? $this->createRouteSummaryDTO($route) : null,
        );
    }

    private function createWarehouseCurrentLocationDTO(StorageContainerLocation $location): WarehouseCurrentLocationDTO
    {
        return new WarehouseCurrentLocationDTO(
            $this->createStorageContainerLocationBasicDTO($location),
            $this->createStorageContainerSummaryDTO($location->getStorageContainer()),
        );
    }

    private function createStorageContainerLocationBasicDTO(StorageContainerLocation $location): StorageContainerLocationBasicDTO
    {
        return new StorageContainerLocationBasicDTO(
            $location->getId()->__toString(),
            $location->getMovedAt()->format(DateTimeInterface::ATOM),
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

    private function createWasteMaterialSummaryDTO(WasteMaterial $wasteMaterial): WasteMaterialSummaryDTO
    {
        return new WasteMaterialSummaryDTO(
            $wasteMaterial->getId()->__toString(),
            $wasteMaterial->getCode(),
            $wasteMaterial->getLabel(),
            $wasteMaterial->getVolumeUnit()->value,
        );
    }

    private function createRouteSummaryDTO(Route $route): RouteSummaryDTO
    {
        return new RouteSummaryDTO(
            $route->getId()->__toString(),
            $route->getDate()->format('Y-m-d'),
            $route->getIsActive(),
        );
    }

    private function createUserDTO(User $user): UserDTO
    {
        return new UserDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getFullName(),
            $user->getIsActive(),
            $user->getIsAdmin(),
        );
    }
}
