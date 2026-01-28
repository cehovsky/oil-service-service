<?php

declare(strict_types=1);

namespace App\OilService;

use App\Files\DBAL\Entity\File;
use App\Files\DBAL\Repository\FileRepository;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Entity\Route;
use App\OilService\DBAL\Entity\User;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\Domain\Error\ErrorCollection;
use App\Domain\Error\ErrorItem;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ValidationException;
use App\OilService\DBAL\Entity\PriceListItem;
use App\OilService\DBAL\Repository\RouteRepository;
use App\OilService\DBAL\Repository\UserRepository;
use App\OilService\DBAL\Repository\PriceListItemRepository;
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
    ) {
    }

    /**
     * @param string[] $priceListItemIds
     */
    public function createOrderWithUser(
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
        ?string $oilChangeVehiclePhotoId,
        ?string $vinPhotoId,
        ?string $oldOilFilterPhotoId,
        ?string $oldOilPhotoId,
        ?string $odometerPhotoId,
        array $otherPhotoIds,
        OrderStatusEnum $status,
        RealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        array $priceListItemIds,
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

        $order = $this->entityFactory->createOrder(
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
            $oilChangeVehiclePhoto,
            $vinPhoto,
            $oldOilFilterPhoto,
            $oldOilPhoto,
            $odometerPhoto,
            $otherPhotos,
            $status,
            $realizationTimeSlot,
            $realizationDate,
            $user,
            $route,
        );

        $this->syncPriceListItems($order, $priceListItems);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    /**
     * @param string[] $priceListItemIds
     */
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
        ?string $oilChangeVehiclePhotoId,
        ?string $vinPhotoId,
        ?string $oldOilFilterPhotoId,
        ?string $oldOilPhotoId,
        ?string $odometerPhotoId,
        array $otherPhotoIds,
        OrderStatusEnum $status,
        RealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        string $userId,
        array $priceListItemIds,
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

        $order = $this->entityFactory->createOrder(
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
            $oilChangeVehiclePhoto,
            $vinPhoto,
            $oldOilFilterPhoto,
            $oldOilPhoto,
            $odometerPhoto,
            $otherPhotos,
            $status,
            $realizationTimeSlot,
            $realizationDate,
            $user,
            $route,
        );

        $this->syncPriceListItems($order, $priceListItems);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    /**
     * @param string[] $priceListItemIds
     */
    public function updateOrder(
        Order $order,
        string $fullName,
        string $phone,
        string $email,
        string $carModel,
        string $licensePlate,
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
        array $otherPhotoIds,
        string $userId,
        bool $routeProvided,
        ?string $routeId,
        array $priceListItemIds,
    ): Order {
        $route = $order->getRoute();

        if ($routeProvided) {
            $route = $this->findRoute($routeId);
        }

        $order->setFullName($fullName);
        $order->setPhone($phone);
        $order->setEmail($email);
        $order->setCarModel($carModel);
        $order->setLicensePlate($licensePlate);
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
        $order->setStatus($status);
        $order->setRoute($route);

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
        array $otherPhotoIds,
    ): Order {
        $order->setOilChangeVehiclePhoto($this->findFile($oilChangeVehiclePhotoId));
        $order->setVinPhoto($this->findFile($vinPhotoId));
        $order->setOldOilFilterPhoto($this->findFile($oldOilFilterPhotoId));
        $order->setOldOilPhoto($this->findFile($oldOilPhotoId));
        $order->setOdometerPhoto($this->findFile($odometerPhotoId));

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
