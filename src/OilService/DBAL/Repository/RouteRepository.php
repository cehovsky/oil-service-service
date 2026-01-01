<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\Route;
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
}
