<?php

declare(strict_types=1);

namespace App\OilService;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Auth\DBAL\Repository\UserRepository as AuthUserRepository;
use App\OilService\DBAL\Entity\Car;
use App\OilService\DBAL\Entity\Route as RouteEntity;
use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Repository\CarRepository;
use App\OilService\DBAL\Repository\OrderRepository;
use App\OilService\DBAL\Repository\TermRepository;
use App\OilService\Factory\EntityFactory;
use App\Warehouse\StorageContainerLocationService;
use App\Warehouse\DBAL\Repository\WarehouseRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouteService
{
    public function __construct(
        private readonly AuthUserRepository $authUserRepository,
        private readonly CarRepository $carRepository,
        private readonly OrderRepository $orderRepository,
        private readonly TermRepository $termRepository,
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageContainerLocationService $storageContainerLocationService,
        private readonly WarehouseRepository $warehouseRepository,
    ) {
    }

    /**
     * @param array<string>|null $termIds
     * @param array<string>|null $storageContainerIds
     * @param array<string>|null $userIds
     */
    public function createRoute(
        ?string $carId,
        bool $isActive,
        DateTimeImmutable $date,
        ?array $termIds,
        ?array $storageContainerIds,
        ?array $userIds,
    ): RouteEntity {
        $car = $this->findCar($carId);
        $route = $this->entityFactory->createRoute($car, $isActive, $date);

        $this->syncRouteTerms($route, $termIds);
        $this->syncRouteStorageContainers($route, $storageContainerIds, $date);
        $this->syncRouteUsers($route, $userIds);

        $this->entityManager->persist($route);
        $this->entityManager->flush();

        return $route;
    }

    /**
     * @param array<string>|null $termIds
     * @param array<string>|null $storageContainerIds
     * @param array<string>|null $userIds
     */
    public function updateRoute(
        RouteEntity $route,
        ?string $carId,
        bool $isActive,
        DateTimeImmutable $date,
        ?array $termIds,
        ?array $storageContainerIds,
        ?array $userIds,
    ): RouteEntity {
        $car = $this->findCar($carId);

        $route->setCar($car);
        $route->setIsActive($isActive);
        $route->setDate($date);

        $this->syncRouteTerms($route, $termIds);
        $this->syncRouteStorageContainers($route, $storageContainerIds, $date);
        $this->syncRouteUsers($route, $userIds);

        $this->entityManager->flush();

        return $route;
    }

    public function deleteRoute(RouteEntity $route): void
    {
        $this->syncRouteTerms($route, []);
        $this->syncRouteUsers($route, []);
        $this->syncRouteOrders($route, []);

        $this->entityManager->remove($route);
        $this->entityManager->flush();
    }

    /**
     * @param string[] $orderIds
     */
    public function updateRouteOrders(RouteEntity $route, array $orderIds): RouteEntity
    {
        $this->syncRouteOrders($route, $orderIds);
        $this->entityManager->flush();

        return $route;
    }

    public function optimizeRouteOrdersByCoordinates(RouteEntity $route): RouteEntity
    {
        $garageWarehouse = $this->warehouseRepository->findFirstActiveGarageWithCoordinates();

        if ($garageWarehouse === null) {
            throw new NotFoundHttpException('Active garage warehouse with coordinates not found');
        }

        $orders = $route->getOrders()->toArray();

        if ($orders === []) {
            return $route;
        }

        $slotOrder = [
            RealizationTimeSlotEnum::MORNING,
            RealizationTimeSlotEnum::LUNCH,
            RealizationTimeSlotEnum::AFTERNOON,
        ];

        $slotsWithCoordinates = [];

        foreach ($slotOrder as $slot) {
            foreach ($orders as $order) {
                if (
                    $order->getRealizationTimeSlot() === $slot
                    && $order->getLatitude() !== null
                    && $order->getLongitude() !== null
                ) {
                    $slotsWithCoordinates[] = $slot;
                    break;
                }
            }
        }

        $finalSlotWithCoordinates = end($slotsWithCoordinates) ?: null;

        $orderedOrders = [];
        $currentLatitude = $garageWarehouse->getLatitude();
        $currentLongitude = $garageWarehouse->getLongitude();

        if ($currentLatitude === null || $currentLongitude === null) {
            throw new NotFoundHttpException('Active garage warehouse with coordinates not found');
        }

        foreach ($slotOrder as $slot) {
            $slotOrders = [];

            foreach ($orders as $order) {
                if ($order->getRealizationTimeSlot() === $slot) {
                    $slotOrders[] = $order;
                }
            }

            if ($slotOrders === []) {
                continue;
            }

            $ordersWithCoordinates = [];
            $ordersWithoutCoordinates = [];

            foreach ($slotOrders as $order) {
                if ($order->getLatitude() !== null && $order->getLongitude() !== null) {
                    $ordersWithCoordinates[] = $order;
                } else {
                    $ordersWithoutCoordinates[] = $order;
                }
            }

            $isFinalSlot = $finalSlotWithCoordinates !== null && $slot === $finalSlotWithCoordinates;

            $orderedSlot = $this->orderOrdersByNearestNeighbor(
                $ordersWithCoordinates,
                $currentLatitude,
                $currentLongitude,
                $isFinalSlot ? $garageWarehouse->getLatitude() : null,
                $isFinalSlot ? $garageWarehouse->getLongitude() : null,
            );

            $orderedOrders = array_merge($orderedOrders, $orderedSlot, $ordersWithoutCoordinates);

            if ($orderedSlot !== []) {
                $lastOrder = $orderedSlot[array_key_last($orderedSlot)];
                $currentLatitude = (float) $lastOrder->getLatitude();
                $currentLongitude = (float) $lastOrder->getLongitude();
            }
        }

        $orderIds = array_map(
            static fn (Order $order): string => $order->getId()->toRfc4122(),
            $orderedOrders
        );

        return $this->updateRouteOrders($route, $orderIds);
    }

    /**
     * @param string[]|null $termIds
     */
    private function syncRouteTerms(RouteEntity $route, ?array $termIds): void
    {
        if ($termIds === null) {
            return;
        }

        foreach ($route->getTerms()->toArray() as $term) {
            $route->removeTerm($term);
        }

        foreach ($termIds as $termId) {
            $term = $this->findTerm($termId);
            $route->addTerm($term);
        }
    }

    private function findCar(?string $carId): ?Car
    {
        if ($carId === null) {
            return null;
        }

        $car = $this->carRepository->find($carId);

        if ($car === null) {
            throw new NotFoundHttpException('Car not found');
        }

        return $car;
    }

    private function findTerm(string $termId): Term
    {
        $term = $this->termRepository->find($termId);

        if ($term === null) {
            throw new NotFoundHttpException('Term not found: ' . $termId);
        }

        return $term;
    }

    /**
     * @param string[]|null $userIds
     */
    private function syncRouteUsers(RouteEntity $route, ?array $userIds): void
    {
        if ($userIds === null) {
            return;
        }

        $existingRouteUsersByUserId = [];

        foreach ($route->getRouteUsers()->toArray() as $routeUser) {
            $existingRouteUsersByUserId[$routeUser->getUser()->getId()->toRfc4122()] = $routeUser;
        }

        foreach (array_unique($userIds) as $userId) {
            if (isset($existingRouteUsersByUserId[$userId])) {
                unset($existingRouteUsersByUserId[$userId]);

                continue; // keep existing link
            }

            $user = $this->findAuthUser($userId);
            $routeUser = $this->entityFactory->createRouteUser($route, $user);
            $route->addRouteUser($routeUser);
        }

        foreach ($existingRouteUsersByUserId as $routeUser) {
            $route->removeRouteUser($routeUser);
            $this->entityManager->remove($routeUser);
        }
    }

    private function findAuthUser(string $userId): AuthUser
    {
        $user = $this->authUserRepository->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException('User not found: ' . $userId);
        }

        return $user;
    }

    /**
     * @param string[]|null $storageContainerIds
     */
    private function syncRouteStorageContainers(RouteEntity $route, ?array $storageContainerIds, DateTimeImmutable $routeDate): void
    {
        if ($storageContainerIds === null) {
            return;
        }

        $movedAt = $routeDate->setTime(0, 0);

        foreach ($route->getStorageContainerLocations()->toArray() as $location) {
            $route->removeStorageContainerLocation($location);
            $this->entityManager->remove($location);
        }

        foreach ($storageContainerIds as $storageContainerId) {
            $this->storageContainerLocationService->createStorageContainerLocation(
                $storageContainerId,
                null,
                $route->getId()->__toString(),
                $movedAt,
            );
        }
    }

    /**
     * @param string[]|null $orderIds
     */
    private function syncRouteOrders(RouteEntity $route, ?array $orderIds): void
    {
        if ($orderIds === null) {
            return;
        }

        $orderIds = array_values(array_unique($orderIds));

        $existingOrdersById = [];

        foreach ($route->getOrders()->toArray() as $order) {
            $existingOrdersById[$order->getId()->toRfc4122()] = $order;
        }

        $position = 1;

        foreach ($orderIds as $orderId) {
            if (isset($existingOrdersById[$orderId])) {
                $order = $existingOrdersById[$orderId];
                unset($existingOrdersById[$orderId]);
            } else {
                $order = $this->findOrder($orderId);
                $order->setRoute($route);
            }

            $order->setRouteOrderPosition($position);
            $position++;
        }

        foreach ($existingOrdersById as $order) {
            $order->setRoute(null);
        }
    }

    /**
     * @param Order[] $orders
     *
     * @return Order[]
     */
    private function orderOrdersByNearestNeighbor(
        array $orders,
        float $startLatitude,
        float $startLongitude,
        ?float $endLatitude,
        ?float $endLongitude,
    ): array {
        if ($orders === []) {
            return [];
        }

        $remaining = array_values($orders);
        $ordered = [];
        $currentLatitude = $startLatitude;
        $currentLongitude = $startLongitude;

        while ($remaining !== []) {
            $closestIndex = null;
            $closestScore = null;

            foreach ($remaining as $index => $candidate) {
                $candidateLatitude = (float) $candidate->getLatitude();
                $candidateLongitude = (float) $candidate->getLongitude();

                $score = $this->calculateDistance(
                    $currentLatitude,
                    $currentLongitude,
                    $candidateLatitude,
                    $candidateLongitude,
                );

                if ($endLatitude !== null && $endLongitude !== null) {
                    $score += $this->calculateDistance(
                        $candidateLatitude,
                        $candidateLongitude,
                        $endLatitude,
                        $endLongitude,
                    );
                }

                if ($closestScore === null || $score < $closestScore) {
                    $closestScore = $score;
                    $closestIndex = $index;
                }
            }

            $closestOrder = $remaining[$closestIndex];
            $ordered[] = $closestOrder;
            array_splice($remaining, $closestIndex, 1);

            $currentLatitude = (float) $closestOrder->getLatitude();
            $currentLongitude = (float) $closestOrder->getLongitude();
        }

        return $ordered;
    }

    private function calculateDistance(
        float $fromLatitude,
        float $fromLongitude,
        float $toLatitude,
        float $toLongitude,
    ): float {
        $earthRadius = 6371.0;

        $latFrom = deg2rad($fromLatitude);
        $lonFrom = deg2rad($fromLongitude);
        $latTo = deg2rad($toLatitude);
        $lonTo = deg2rad($toLongitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2)
            + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return $earthRadius * $angle;
    }

    private function findOrder(string $orderId): Order
    {
        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            throw new NotFoundHttpException('Order not found: ' . $orderId);
        }

        return $order;
    }
}
