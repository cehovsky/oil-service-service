<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\Term;
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

        $qb->leftJoin(self::ALIAS . '.forms', 'f');
        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.isActive', ':isActive'));
        $qb->andWhere($qb->expr()->gte(self::ALIAS . '.date', ':dateFrom'));
        $qb->groupBy(self::ALIAS . '.id');
        $qb->having($qb->expr()->lt('COUNT(f.id)', self::ALIAS . '.maxCount'));
        $qb->orderBy(self::ALIAS . '.date', 'ASC');
        $qb->addOrderBy(self::ALIAS . '.timeSlot', 'ASC');

        $qb->setParameter('isActive', true);
        $qb->setParameter('dateFrom', $dateFrom->setTime(0, 0));

        return $qb->getQuery()->getResult();
    }
}
