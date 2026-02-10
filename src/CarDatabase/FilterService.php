<?php

declare(strict_types=1);

namespace App\CarDatabase;

use App\CarDatabase\DBAL\Entity\Filter;
use App\CarDatabase\DBAL\Enum\FilterTypeEnum;
use App\CarDatabase\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class FilterService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createFilter(
        FilterTypeEnum $filterType,
        string $manufacturer,
        string $code,
        ?string $oemCode,
        ?string $thread,
        ?int $heightMm,
        ?int $diameterMm,
        ?string $notes,
    ): Filter {
        $filter = $this->entityFactory->createFilter(
            $filterType,
            $manufacturer,
            $code,
            $oemCode,
            $thread,
            $heightMm,
            $diameterMm,
            $notes,
        );

        $this->entityManager->persist($filter);
        $this->entityManager->flush();

        return $filter;
    }

    public function updateFilter(
        Filter $filter,
        FilterTypeEnum $filterType,
        string $manufacturer,
        string $code,
        ?string $oemCode,
        ?string $thread,
        ?int $heightMm,
        ?int $diameterMm,
        ?string $notes,
    ): Filter {
        $filter->setFilterType($filterType);
        $filter->setManufacturer($manufacturer);
        $filter->setCode($code);
        $filter->setOemCode($oemCode);
        $filter->setThread($thread);
        $filter->setHeightMm($heightMm);
        $filter->setDiameterMm($diameterMm);
        $filter->setNotes($notes);
        $filter->setUpdatedAt(new DateTimeImmutable());

        $this->entityManager->flush();

        return $filter;
    }

    public function deleteFilter(Filter $filter): void
    {
        $this->entityManager->remove($filter);
        $this->entityManager->flush();
    }
}
