<?php

declare(strict_types=1);

namespace App\OilService;

use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class TermService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createTerm(
        DateTimeImmutable $date,
        RealizationTimeSlotEnum $timeSlot,
        bool $isActive,
        int $maxCount,
    ): Term {
        $term = $this->entityFactory->createTerm(
            $date,
            $timeSlot,
            $isActive,
            $maxCount,
        );

        $this->entityManager->persist($term);
        $this->entityManager->flush();

        return $term;
    }

    public function updateTerm(
        Term $term,
        DateTimeImmutable $date,
        RealizationTimeSlotEnum $timeSlot,
        bool $isActive,
        int $maxCount,
    ): Term {
        $term->setDate($date);
        $term->setTimeSlot($timeSlot);
        $term->setIsActive($isActive);
        $term->setMaxCount($maxCount);

        $this->entityManager->flush();

        return $term;
    }

    public function deleteTerm(Term $term): void
    {
        foreach ($term->getRoutes()->toArray() as $route) {
            $route->removeTerm($term);
        }

        $this->entityManager->remove($term);
        $this->entityManager->flush();
    }
}
