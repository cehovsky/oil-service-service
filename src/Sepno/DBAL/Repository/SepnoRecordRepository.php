<?php

declare(strict_types=1);

namespace App\Sepno\DBAL\Repository;

use App\OilService\DBAL\Entity\Route;
use App\Sepno\DBAL\Entity\SepnoRecord;
use App\Sepno\DBAL\Enum\SepnoRecordStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SepnoRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method SepnoRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method SepnoRecord[] findAll()
 * @method SepnoRecord[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<SepnoRecord>
 */
class SepnoRecordRepository extends ServiceEntityRepository
{
    public const ALIAS = 'ssr';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SepnoRecord::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    /**
     * @return SepnoRecord[]
     */
    public function findByRoute(Route $route): array
    {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.route', ':route'))
            ->setParameter('route', $route)
            ->orderBy(self::ALIAS . '.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findCurrentForRoute(Route $route): ?SepnoRecord
    {
        $current = $route->getCurrentSepnoRecord();

        if ($current !== null) {
            return $current;
        }

        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.route', ':route'))
            ->setParameter('route', $route)
            ->orderBy(self::ALIAS . '.createdAt', 'DESC')
            ->setMaxResults(1);

        /** @var SepnoRecord|null $record */
        $record = $qb->getQuery()->getOneOrNullResult();

        return $record;
    }

    /**
     * @return array{records: SepnoRecord[], total: int}
     */
    public function findPaged(
        int $page,
        int $perPage,
        ?string $routeId = null,
        ?SepnoRecordStatusEnum $status = null,
    ): array {
        $qb = $this->createQueryBuilder(self::ALIAS)
            ->orderBy(self::ALIAS . '.createdAt', 'DESC');

        if ($routeId !== null) {
            $qb->andWhere($qb->expr()->eq(self::ALIAS . '.route', ':routeId'))
                ->setParameter('routeId', $routeId);
        }

        if ($status !== null) {
            $qb->andWhere($qb->expr()->eq(self::ALIAS . '.status', ':status'))
                ->setParameter('status', $status);
        }

        $countQb = clone $qb;
        $countQb->select('COUNT(' . self::ALIAS . '.id)');

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        $qb->setFirstResult(max(0, ($page - 1) * $perPage));
        $qb->setMaxResults($perPage);

        /** @var SepnoRecord[] $records */
        $records = $qb->getQuery()->getResult();

        return [
            'records' => $records,
            'total' => $total,
        ];
    }
}
