<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Repository;

use App\Warehouse\DBAL\Entity\Recycling;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recycling|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recycling|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recycling[] findAll()
 * @method Recycling[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<Recycling>
 */
class RecyclingRepository extends ServiceEntityRepository
{
    public const ALIAS = 'wrc';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recycling::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }
}
