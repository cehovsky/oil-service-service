<?php

declare(strict_types=1);

namespace App\CarDatabase;

use App\CarDatabase\DBAL\Entity\Engine;
use App\CarDatabase\DBAL\Entity\EngineFilter;
use App\CarDatabase\DBAL\Entity\Filter;
use App\CarDatabase\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class EngineFilterService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createEngineFilter(
        Engine $engine,
        Filter $filter,
        bool $isPrimary,
        ?string $source,
    ): EngineFilter {
        $engineFilter = $this->entityFactory->createEngineFilter(
            $engine,
            $filter,
            $isPrimary,
            $source,
        );

        $this->entityManager->persist($engineFilter);
        $this->entityManager->flush();

        return $engineFilter;
    }

    public function updateEngineFilter(
        EngineFilter $engineFilter,
        Engine $engine,
        Filter $filter,
        bool $isPrimary,
        ?string $source,
    ): EngineFilter {
        $engineFilter->setEngine($engine);
        $engineFilter->setFilter($filter);
        $engineFilter->setIsPrimary($isPrimary);
        $engineFilter->setSource($source);
        $engineFilter->setUpdatedAt(new DateTimeImmutable());

        $this->entityManager->flush();

        return $engineFilter;
    }

    public function deleteEngineFilter(EngineFilter $engineFilter): void
    {
        $this->entityManager->remove($engineFilter);
        $this->entityManager->flush();
    }
}
