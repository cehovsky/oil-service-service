<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\OilService\DBAL\Entity\Route;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Route|null find($id, $lockMode = null, $lockVersion = null)
 * @method Route|null findOneBy(array $criteria, array $orderBy = null)
 * @method Route[] findAll()
 * @method Route[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<Route>
 */
class RouteRepository extends ServiceEntityRepository
{
    public const ALIAS = 'osr';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Route::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    /**
     * @return Route[]
     */
    public function findUpcomingActiveRoutes(DateTimeImmutable $fromDate, int $maxResults = 10): array
    {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.isActive', ':isActive'))
            ->andWhere($qb->expr()->gte(self::ALIAS . '.date', ':fromDate'))
            ->orderBy(self::ALIAS . '.date', 'ASC')
            ->addOrderBy(self::ALIAS . '.createdAt', 'ASC')
            ->setMaxResults($maxResults)
            ->setParameter('isActive', true)
            ->setParameter('fromDate', $fromDate);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Route[]
     */
    public function findActiveRoutesForUserOnDate(AuthUser $user, DateTimeImmutable $date): array
    {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->innerJoin(self::ALIAS . '.routeUsers', 'ru')
            ->andWhere('ru.user = :user')
            ->andWhere($qb->expr()->eq(self::ALIAS . '.date', ':date'))
            ->andWhere($qb->expr()->eq(self::ALIAS . '.isActive', ':isActive'))
            ->orderBy(self::ALIAS . '.createdAt', 'ASC')
            ->setParameter('user', $user)
            ->setParameter('date', $date)
            ->setParameter('isActive', true);

        return $qb->getQuery()->getResult();
    }
}
