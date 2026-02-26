<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Entity\Route;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[] findAll()
 * @method Order[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public const ALIAS = 'oso';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    public function countActiveByDateAndTimeSlot(
        DateTimeImmutable $date,
        RealizationTimeSlotEnum $timeSlot,
    ): int {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->select('COUNT(' . self::ALIAS . '.id)')
            ->andWhere($qb->expr()->eq(self::ALIAS . '.realizationDate', ':realizationDate'))
            ->andWhere($qb->expr()->eq(self::ALIAS . '.realizationTimeSlot', ':realizationTimeSlot'))
            ->andWhere($qb->expr()->neq(self::ALIAS . '.status', ':canceledStatus'))
            ->setParameter('realizationDate', $date)
            ->setParameter('realizationTimeSlot', $timeSlot)
            ->setParameter('canceledStatus', OrderStatusEnum::CANCELED->value);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return array<string, int> keyed by "Y-m-d|timeSlot"
     */
    public function getActiveOrderCountsByDateRange(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): array {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->select(self::ALIAS . '.realizationDate AS realizationDate')
            ->addSelect(self::ALIAS . '.realizationTimeSlot AS realizationTimeSlot')
            ->addSelect('COUNT(' . self::ALIAS . '.id) AS orderCount')
            ->andWhere($qb->expr()->gte(self::ALIAS . '.realizationDate', ':dateFrom'))
            ->andWhere($qb->expr()->lte(self::ALIAS . '.realizationDate', ':dateTo'))
            ->andWhere($qb->expr()->neq(self::ALIAS . '.status', ':canceledStatus'))
            ->groupBy(self::ALIAS . '.realizationDate')
            ->addGroupBy(self::ALIAS . '.realizationTimeSlot')
            ->orderBy(self::ALIAS . '.realizationDate', 'ASC')
            ->addOrderBy(self::ALIAS . '.realizationTimeSlot', 'ASC')
            ->setParameter('dateFrom', $from)
            ->setParameter('dateTo', $to)
            ->setParameter('canceledStatus', OrderStatusEnum::CANCELED->value);

        $results = $qb->getQuery()->getArrayResult();

        /** @var array<array<string, mixed>> $results */
        $counts = [];

        foreach ($results as $row) {
            if (!isset($row['realizationDate'], $row['realizationTimeSlot'], $row['orderCount'])) {
                continue;
            }

            $date = $row['realizationDate'];
            $timeSlot = $row['realizationTimeSlot'];
            $count = is_numeric($row['orderCount']) ? (int) $row['orderCount'] : 0;

            if ($date instanceof DateTimeInterface && $timeSlot instanceof RealizationTimeSlotEnum) {
                $key = $date->format('Y-m-d') . '|' . $timeSlot->value;
                $counts[$key] = $count;
            }
        }

        return $counts;
    }

    /**
     * Returns the next available ident number.
     */
    public function getNextIdent(): int
    {
        $qb = $this->createQueryBuilder('o');
        $qb->select('MAX(o.ident)');

        $maxIdent = $qb->getQuery()->getSingleScalarResult();

        return $maxIdent !== null ? ((int) $maxIdent) + 1 : 1;
    }

    public function getMaxRouteOrderPosition(Route $route): int
    {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->select('MAX(' . self::ALIAS . '.routeOrderPosition)')
            ->andWhere($qb->expr()->eq(self::ALIAS . '.route', ':route'))
            ->setParameter('route', $route);

        $maxPosition = $qb->getQuery()->getSingleScalarResult();

        return $maxPosition !== null ? (int) $maxPosition : 0;
    }

    public function findOneBySecretKey(string $secretKey): ?Order
    {
        return $this->findOneBy(['secretKey' => $secretKey]);
    }

    /**
     * Find orders by ident number or formatted ident (OYYXXXXX format).
     *
     * @return Order[]
     */
    public function findByIdentFilter(string $identFilter): array
    {
        $qb = $this->createQueryBuilder(self::ALIAS);

        // If the filter starts with 'O' or 'o', try to parse it as formatted ident
        if (preg_match('/^[Oo](\d{2})(\d+)$/', $identFilter, $matches)) {
            $identNumber = (int) $matches[2];
            $qb->andWhere($qb->expr()->eq(self::ALIAS . '.ident', ':ident'));
            $qb->setParameter('ident', $identNumber);
        } elseif (is_numeric($identFilter)) {
            // Direct numeric filter
            $qb->andWhere($qb->expr()->eq(self::ALIAS . '.ident', ':ident'));
            $qb->setParameter('ident', (int) $identFilter);
        }

        return $qb->getQuery()->getResult();
    }
}
