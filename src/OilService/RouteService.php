<?php

declare(strict_types=1);

namespace App\OilService;

use App\OilService\DBAL\Entity\Car;
use App\OilService\DBAL\Entity\Route as RouteEntity;
use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Repository\CarRepository;
use App\OilService\DBAL\Repository\TermRepository;
use App\OilService\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouteService
{
    public function __construct(
        private readonly CarRepository $carRepository,
        private readonly TermRepository $termRepository,
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createRoute(
        ?string $carId,
        bool $isActive,
        DateTimeImmutable $date,
        ?array $termIds,
    ): RouteEntity {
        $car = $this->findCar($carId);
        $route = $this->entityFactory->createRoute($car, $isActive, $date);

        $this->syncRouteTerms($route, $termIds);

        $this->entityManager->persist($route);
        $this->entityManager->flush();

        return $route;
    }

    public function updateRoute(
        RouteEntity $route,
        ?string $carId,
        bool $isActive,
        DateTimeImmutable $date,
        ?array $termIds,
    ): RouteEntity {
        $car = $this->findCar($carId);

        $route->setCar($car);
        $route->setIsActive($isActive);
        $route->setDate($date);

        $this->syncRouteTerms($route, $termIds);

        $this->entityManager->flush();

        return $route;
    }

    public function deleteRoute(RouteEntity $route): void
    {
        $this->syncRouteTerms($route, []);

        $this->entityManager->remove($route);
        $this->entityManager->flush();
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
}
