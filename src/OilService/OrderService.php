<?php

declare(strict_types=1);

namespace App\OilService;

use App\Domain\Error\ErrorCollection;
use App\Domain\Error\ErrorItem;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ValidationException;
use App\Files\DBAL\Entity\File;
use App\Files\DBAL\Repository\FileRepository;
use App\Geocoding\GeocodingResult;
use App\Geocoding\GeocodingService;
use App\OilService\DBAL\Entity\CustomerCar;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Entity\PriceListItem;
use App\OilService\DBAL\Entity\Route;
use App\OilService\DBAL\Entity\User;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Repository\CustomerCarRepository;
use App\OilService\DBAL\Repository\OrderRepository;
use App\OilService\DBAL\Repository\PriceListItemRepository;
use App\OilService\DBAL\Repository\RouteRepository;
use App\OilService\DBAL\Repository\UserRepository;
use App\OilService\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RouteRepository $routeRepository,
        private readonly PriceListItemRepository $priceListItemRepository,
        private readonly FileRepository $fileRepository,
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly OrderRepository $orderRepository,
        private readonly CustomerCarRepository $customerCarRepository,
        private readonly CustomerCarService $customerCarService,
        private readonly GeocodingService $geocodingService,
    ) {
    }

    /**
     * @param string[] $priceListItemIds
     * @param string[] $otherPhotoIds
     */
    public function createOrderWithUser(
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
        ?string $oilChangeVehiclePhotoId,
        ?string $vinPhotoId,
        ?string $oldOilFilterPhotoId,
        ?string $oldOilPhotoId,
        ?string $odometerPhotoId,
        ?int $mileage,
        array $otherPhotoIds,
        OrderStatusEnum $status,
        RealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        array $priceListItemIds,
        ?string $customerCarId = null,
        ?Route $route = null,
    ): Order {
        if ($route !== null) {
            $realizationDate = $route->getDate();
        }

        $user = $this->findOrCreateUser($email, $phone, $fullName);

        $priceListItems = $this->resolvePublicPriceListItems($priceListItemIds);

        $oilChangeVehiclePhoto = $this->findFile($oilChangeVehiclePhotoId);
        $vinPhoto = $this->findFile($vinPhotoId);
        $oldOilFilterPhoto = $this->findFile($oldOilFilterPhotoId);
        $oldOilPhoto = $this->findFile($oldOilPhotoId);
        $odometerPhoto = $this->findFile($odometerPhotoId);
        $otherPhotos = $this->resolveFilesByIds($otherPhotoIds);

        $customerCar = $this->findCustomerCar($customerCarId);
        if ($customerCar !== null) {
            $this->customerCarService->assignUser($customerCar, $user);
        }

        $order = $this->entityFactory->createOrder(
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
            $route,
            $customerCar,
        );

        if ($route !== null) {
            $order->setRouteOrderPosition(
                $this->orderRepository->getMaxRouteOrderPosition($route) + 1
            );
        }

        $this->syncPriceListItems($order, $priceListItems);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    /**
     * @param string[] $priceListItemIds
     * @param string[] $otherPhotoIds
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
        ?string $oilChangeVehiclePhotoId,
        ?string $vinPhotoId,
        ?string $oldOilFilterPhotoId,
        ?string $oldOilPhotoId,
        ?string $odometerPhotoId,
        ?int $mileage,
        array $otherPhotoIds,
        OrderStatusEnum $status,
        RealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        string $userId,
        array $priceListItemIds,
        ?string $customerCarId = null,
        ?Route $route = null,
    ): Order {
        if ($route !== null) {
            $realizationDate = $route->getDate();
        }

        $user = $this->findUser($userId);

        $priceListItems = $this->resolveAdminPriceListItems($priceListItemIds);

        $oilChangeVehiclePhoto = $this->findFile($oilChangeVehiclePhotoId);
        $vinPhoto = $this->findFile($vinPhotoId);
        $oldOilFilterPhoto = $this->findFile($oldOilFilterPhotoId);
        $oldOilPhoto = $this->findFile($oldOilPhotoId);
        $odometerPhoto = $this->findFile($odometerPhotoId);
        $otherPhotos = $this->resolveFilesByIds($otherPhotoIds);

        $customerCar = $this->findCustomerCar($customerCarId);
        if ($customerCar !== null) {
            $this->customerCarService->assignUser($customerCar, $user);
        }

        $order = $this->entityFactory->createOrder(
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
            $route,
            $customerCar,
        );

        if ($route !== null) {
            $order->setRouteOrderPosition(
                $this->orderRepository->getMaxRouteOrderPosition($route) + 1
            );
        }

        $this->syncPriceListItems($order, $priceListItems);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    /**
     * @param string[] $priceListItemIds
     * @param string[] $otherPhotoIds
     */
    public function updateOrder(
        Order $order,
        string $fullName,
        string $phone,
        string $email,
        string $carModel,
        string $licensePlate,
        ?string $vin,
        string $address,
        ?string $note,
        OrderStatusEnum $status,
        RealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        bool $isCompany,
        ?string $companyName,
        ?string $companyIdentificationNumber,
        ?string $companyTaxId,
        ?string $companyAddress,
        ?string $oilChangeVehiclePhotoId,
        ?string $vinPhotoId,
        ?string $oldOilFilterPhotoId,
        ?string $oldOilPhotoId,
        ?string $odometerPhotoId,
        ?int $mileage,
        array $otherPhotoIds,
        string $userId,
        bool $routeProvided,
        ?string $routeId,
        array $priceListItemIds,
        ?string $customerCarId,
    ): Order {
        $previousRoute = $order->getRoute();
        $route = $previousRoute;

        if ($routeProvided) {
            $route = $this->findRoute($routeId);
        }

        $order->setFullName($fullName);
        $order->setPhone($phone);
        $order->setEmail($email);
        $order->setCarModel($carModel);
        $order->setLicensePlate($licensePlate);
        $order->setVin($vin);
        $order->setAddress($address);
        $order->setNote($note);
        $order->setIsCompany($isCompany);
        $order->setCompanyName($companyName);
        $order->setCompanyIdentificationNumber($companyIdentificationNumber);
        $order->setCompanyTaxId($companyTaxId);
        $order->setCompanyAddress($companyAddress);
        $order->setOilChangeVehiclePhoto($this->findFile($oilChangeVehiclePhotoId));
        $order->setVinPhoto($this->findFile($vinPhotoId));
        $order->setOldOilFilterPhoto($this->findFile($oldOilFilterPhotoId));
        $order->setOldOilPhoto($this->findFile($oldOilPhotoId));
        $order->setOdometerPhoto($this->findFile($odometerPhotoId));
        $order->setMileage($mileage);
        $order->setStatus($status);
        $order->setRoute($route);

        if ($routeProvided && $route !== $previousRoute) {
            if ($route === null) {
                $order->setRouteOrderPosition(null);
            } else {
                $order->setRouteOrderPosition(
                    $this->orderRepository->getMaxRouteOrderPosition($route) + 1
                );
            }
        }

        if ($route !== null) {
            $order->setRealizationDate($route->getDate());
            $order->setRealizationTimeSlot($realizationTimeSlot);
        } else {
            $order->setRealizationTimeSlot($realizationTimeSlot);
            $order->setRealizationDate($realizationDate);
        }

        $user = $order->getUser();

        if ($user->getId()->__toString() !== $userId) {
            $user = $this->findUser($userId);
        }

        $order->setUser($user);

        $this->syncPriceListItems($order, $this->resolvePriceListItemsByIds($priceListItemIds));
        $this->syncOtherPhotos($order, $this->resolveFilesByIds($otherPhotoIds));

        $customerCar = $this->findCustomerCar($customerCarId);
        $order->setCustomerCar($customerCar);
        if ($customerCar !== null) {
            $this->customerCarService->assignUser($customerCar, $order->getUser());
        }

        $this->entityManager->flush();

        return $order;
    }

    /**
     * @param string[] $otherPhotoIds
     */
    public function updateOrderPhotos(
        Order $order,
        ?string $oilChangeVehiclePhotoId,
        ?string $vinPhotoId,
        ?string $oldOilFilterPhotoId,
        ?string $oldOilPhotoId,
        ?string $odometerPhotoId,
        ?int $mileage,
        array $otherPhotoIds,
    ): Order {
        $order->setOilChangeVehiclePhoto($this->findFile($oilChangeVehiclePhotoId));
        $order->setVinPhoto($this->findFile($vinPhotoId));
        $order->setOldOilFilterPhoto($this->findFile($oldOilFilterPhotoId));
        $order->setOldOilPhoto($this->findFile($oldOilPhotoId));
        $order->setOdometerPhoto($this->findFile($odometerPhotoId));
        $order->setMileage($mileage);

        $this->syncOtherPhotos($order, $this->resolveFilesByIds($otherPhotoIds));

        $this->entityManager->flush();

        return $order;
    }

    public function updateOrderStatus(Order $order, OrderStatusEnum $status): Order
    {
        $order->setStatus($status);
        $this->entityManager->flush();

        return $order;
    }

    public function updateOrderCoordinates(Order $order, ?float $latitude, ?float $longitude): Order
    {
        $order->setLatitude($latitude);
        $order->setLongitude($longitude);

        $this->entityManager->flush();

        return $order;
    }

    public function resolveCustomerCarFromOrder(Order $order): CustomerCar
    {
        $existingCar = $order->getCustomerCar();

        if ($existingCar !== null) {
            return $existingCar;
        }

        $vin = $order->getVin();
        $licensePlate = $order->getLicensePlate();

        $car = null;

        if ($vin !== null && $vin !== '') {
            $car = $this->customerCarRepository->findOneByVin($vin);
        }

        if ($car === null) {
            $car = $this->customerCarRepository->findOneByLicensePlate($licensePlate);
        }

        if ($car === null) {
            $car = $this->customerCarService->createCustomerCar(
                $licensePlate,
                null,
                $order->getCarModel(),
                $vin,
                $order->getUser(),
            );

            if ($vin !== null && $vin !== '') {
                $this->customerCarService->updateFromDataCube($car, $vin);
            }
        } else {
            if ($vin !== null && $vin !== '' && $car->getVin() === null) {
                $car->setVin($vin);
            }

            $this->customerCarService->assignUser($car, $order->getUser());
        }

        $order->setCustomerCar($car);
        $this->entityManager->flush();

        return $car;
    }

    public function findCustomerCarConflict(Order $order): ?CustomerCar
    {
        $vin = $order->getVin();
        $licensePlate = $order->getLicensePlate();

        $car = null;

        if ($vin !== null && $vin !== '') {
            $car = $this->customerCarRepository->findOneByVin($vin);
        }

        if ($car === null) {
            $car = $this->customerCarRepository->findOneByLicensePlate($licensePlate);
        }

        if ($car === null) {
            return null;
        }

        $assignedUser = $car->getUser();
        if ($assignedUser === null) {
            return null;
        }

        if ($assignedUser->getId()->__toString() === $order->getUser()->getId()->__toString()) {
            return null;
        }

        return $car;
    }

    public function resolveOrderCoordinatesFromAddress(Order $order): GeocodingResult
    {
        $result = $this->geocodingService->geocodeAddress($order->getAddress());

        if (!$result->isSuccess()) {
            return $result;
        }

        $order->setLatitude($result->getLatitude());
        $order->setLongitude($result->getLongitude());

        $this->entityManager->flush();

        return $result;
    }

    public function deleteOrder(Order $order): void
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }

    public function createRealizationDate(string $realizationDate): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $realizationDate);

        if ($date === false) {
            throw new InvalidDataException('Invalid realization date format.');
        }

        return $date;
    }

    private function findOrCreateUser(string $email, string $phone, string $fullName): User
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user !== null) {
            return $user;
        }

        $user = $this->entityFactory->createUser($email, $phone, $fullName);
        $this->entityManager->persist($user);

        return $user;
    }

    private function findRoute(?string $routeId): ?Route
    {
        if ($routeId === null) {
            return null;
        }

        $route = $this->routeRepository->find($routeId);

        if ($route === null) {
            throw new NotFoundHttpException();
        }

        return $route;
    }

    private function findUser(string $userId): User
    {
        $user = $this->userRepository->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        return $user;
    }

    private function findCustomerCar(?string $customerCarId): ?CustomerCar
    {
        if ($customerCarId === null) {
            return null;
        }

        $car = $this->customerCarRepository->find($customerCarId);

        if ($car === null) {
            throw new NotFoundHttpException();
        }

        return $car;
    }

    /**
     * @param string[] $priceListItemIds
     *
     * @return PriceListItem[]
     */
    private function resolveAdminPriceListItems(array $priceListItemIds): array
    {
        $selectedItems = $this->resolvePriceListItemsByIds($priceListItemIds);
        $defaultItems = $this->priceListItemRepository->findDefaultActiveItems();

        return $this->mergePriceListItems($selectedItems, $defaultItems);
    }

    /**
     * @param string[] $priceListItemIds
     *
     * @return PriceListItem[]
     */
    private function resolvePublicPriceListItems(array $priceListItemIds): array
    {
        $normalizedIds = array_values(array_unique($priceListItemIds));
        $selectedItems = $this->priceListItemRepository->findActiveVisibleNonDefaultByIds($normalizedIds);

        if (count($selectedItems) !== count($normalizedIds)) {
            throw $this->createInvalidPriceListItemsException();
        }

        $defaultItems = $this->priceListItemRepository->findDefaultActiveItems();

        return $this->mergePriceListItems($selectedItems, $defaultItems);
    }

    /**
     * @param string[] $priceListItemIds
     *
     * @return PriceListItem[]
     */
    private function resolvePriceListItemsByIds(array $priceListItemIds): array
    {
        return $this->priceListItemRepository->findByIds($priceListItemIds);
    }

    /**
     * @param string[] $fileIds
     * @return File[]
     */
    private function resolveFilesByIds(array $fileIds): array
    {
        if ($fileIds === []) {
            return [];
        }

        $normalizedIds = array_values(array_unique($fileIds));
        $files = $this->fileRepository->findBy(['id' => $normalizedIds]);

        if (count($files) !== count($normalizedIds)) {
            throw new InvalidDataException('Invalid file id.');
        }

        return $files;
    }

    /**
     * @param PriceListItem[] ...$itemsCollections
     *
     * @return PriceListItem[]
     */
    private function mergePriceListItems(array ...$itemsCollections): array
    {
        $itemsById = [];

        foreach ($itemsCollections as $items) {
            foreach ($items as $item) {
                $itemsById[$item->getId()->__toString()] = $item;
            }
        }

        return array_values($itemsById);
    }

    /**
     * @param PriceListItem[] $priceListItems
     */
    private function syncPriceListItems(Order $order, array $priceListItems): void
    {
        $itemsById = [];

        foreach ($priceListItems as $priceListItem) {
            $itemsById[$priceListItem->getId()->__toString()] = $priceListItem;
        }

        foreach ($order->getPriceListItems()->toArray() as $existingItem) {
            if (!isset($itemsById[$existingItem->getId()->__toString()])) {
                $order->removePriceListItem($existingItem);
            }
        }

        foreach ($itemsById as $priceListItem) {
            if (!$order->getPriceListItems()->contains($priceListItem)) {
                $order->addPriceListItem($priceListItem);
            }
        }
    }

    /**
     * @param File[] $otherPhotos
     */
    private function syncOtherPhotos(Order $order, array $otherPhotos): void
    {
        $photosById = [];

        foreach ($otherPhotos as $otherPhoto) {
            $photosById[$otherPhoto->getId()->__toString()] = $otherPhoto;
        }

        foreach ($order->getOtherPhotos()->toArray() as $existingPhoto) {
            if (!isset($photosById[$existingPhoto->getId()->__toString()])) {
                $order->removeOtherPhoto($existingPhoto);
            }
        }

        foreach ($photosById as $otherPhoto) {
            if (!$order->getOtherPhotos()->contains($otherPhoto)) {
                $order->addOtherPhoto($otherPhoto);
            }
        }
    }

    private function findFile(?string $fileId): ?File
    {
        if ($fileId === null) {
            return null;
        }

        $file = $this->fileRepository->find($fileId);

        if ($file === null) {
            throw new InvalidDataException('Invalid file id.');
        }

        return $file;
    }

    private function createInvalidPriceListItemsException(): ValidationException
    {
        $errorCollection = new ErrorCollection();
        $errorCollection->add(
            new ErrorItem(
                'Selected price list items are not available for public orders.',
                'invalidPriceListItems',
                null,
            )
        );

        return new ValidationException(errorCollection: $errorCollection);
    }
}
