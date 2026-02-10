<?php

declare(strict_types=1);

namespace App\CarDatabase\DBAL\Repository;

use App\CarDatabase\DBAL\Entity\EngineFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EngineFilter|null find($id, $lockMode = null, $lockVersion = null)
 * @method EngineFilter|null findOneBy(array $criteria, array $orderBy = null)
 * @method EngineFilter[] findAll()
 * @method EngineFilter[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<EngineFilter>
 */
class EngineFilterRepository extends ServiceEntityRepository
{
    public const ALIAS = 'cdef';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EngineFilter::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }
}
