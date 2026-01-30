<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Repository;

use App\Warehouse\DBAL\Entity\Warehouse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Warehouse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Warehouse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Warehouse[] findAll()
 * @method Warehouse[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<Warehouse>
 */
class WarehouseRepository extends ServiceEntityRepository
{
    public const ALIAS = 'ww';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Warehouse::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    public function findFirstActiveGarageWithCoordinates(): ?Warehouse
    {
        return $this->createQueryBuilder(self::ALIAS)
            ->andWhere(self::ALIAS . '.isActive = :isActive')
            ->andWhere(self::ALIAS . '.isGarage = :isGarage')
            ->andWhere(self::ALIAS . '.latitude IS NOT NULL')
            ->andWhere(self::ALIAS . '.longitude IS NOT NULL')
            ->setParameter('isActive', true)
            ->setParameter('isGarage', true)
            ->orderBy(self::ALIAS . '.createdAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
