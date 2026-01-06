<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Term|null find($id, $lockMode = null, $lockVersion = null)
 * @method Term|null findOneBy(array $criteria, array $orderBy = null)
 * @method Term[] findAll()
 * @method Term[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<Term>
 */
class TermRepository extends ServiceEntityRepository
{
    public const ALIAS = 'ost';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Term::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    /**
     * @return Term[]
     */
    public function findUpcomingAvailableTerms(DateTimeImmutable $dateFrom): array
    {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $subQb = $this->getEntityManager()->createQueryBuilder();

        $subQb->select('COUNT(o.id)')
            ->from(Order::class, 'o')
            ->andWhere($subQb->expr()->eq('o.realizationDate', self::ALIAS . '.date'))
            ->andWhere($subQb->expr()->eq('o.realizationTimeSlot', self::ALIAS . '.timeSlot'))
            ->andWhere($subQb->expr()->neq('o.status', ':canceledStatus'));

        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.isActive', ':isActive'))
            ->andWhere($qb->expr()->gte(self::ALIAS . '.date', ':dateFrom'))
            ->andWhere(
                $qb->expr()->gt(
                    self::ALIAS . '.maxCount',
                    '(' . $subQb->getDQL() . ')'
                )
            )
            ->orderBy(self::ALIAS . '.date', 'ASC')
            ->addOrderBy(self::ALIAS . '.timeSlot', 'ASC')
            ->setParameter('isActive', true)
            ->setParameter('dateFrom', $dateFrom->setTime(0, 0))
            ->setParameter('canceledStatus', OrderStatusEnum::CANCELED->value);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Term[]
     */
    public function findByDateRange(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->andWhere($qb->expr()->gte(self::ALIAS . '.date', ':dateFrom'))
            ->andWhere($qb->expr()->lte(self::ALIAS . '.date', ':dateTo'))
            ->orderBy(self::ALIAS . '.date', 'ASC')
            ->addOrderBy(self::ALIAS . '.timeSlot', 'ASC')
            ->setParameter('dateFrom', $from)
            ->setParameter('dateTo', $to);

        return $qb->getQuery()->getResult();
    }

    public function existsByDateAndTimeSlot(
        DateTimeImmutable $date,
        RealizationTimeSlotEnum $timeSlot,
        ?string $ignoreId = null,
    ): bool {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->select($qb->expr()->count(self::ALIAS . '.id'))
            ->andWhere($qb->expr()->eq(self::ALIAS . '.date', ':date'))
            ->andWhere($qb->expr()->eq(self::ALIAS . '.timeSlot', ':timeSlot'))
            ->setParameter('date', $date)
            ->setParameter('timeSlot', $timeSlot);

        if ($ignoreId !== null) {
            $qb->andWhere($qb->expr()->neq(self::ALIAS . '.id', ':ignoreId'))
                ->setParameter('ignoreId', $ignoreId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
