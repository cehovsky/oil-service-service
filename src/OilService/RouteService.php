<?php

declare(strict_types=1);

namespace App\OilService;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Auth\DBAL\Repository\UserRepository as AuthUserRepository;
use App\OilService\DBAL\Entity\Car;
use App\OilService\DBAL\Entity\Route as RouteEntity;
use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Repository\CarRepository;
use App\OilService\DBAL\Repository\OrderRepository;
use App\OilService\DBAL\Repository\TermRepository;
use App\OilService\Factory\EntityFactory;
use App\Warehouse\StorageContainerLocationService;
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

        foreach ($orderIds as $orderId) {
            if (isset($existingOrdersById[$orderId])) {
                unset($existingOrdersById[$orderId]);

                continue;
            }

            $order = $this->findOrder($orderId);
            $order->setRoute($route);
        }

        foreach ($existingOrdersById as $order) {
            $order->setRoute(null);
        }
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
