<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Repository;

use App\Warehouse\DBAL\Entity\WasteMaterial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WasteMaterial|null find($id, $lockMode = null, $lockVersion = null)
 * @method WasteMaterial|null findOneBy(array $criteria, array $orderBy = null)
 * @method WasteMaterial[] findAll()
 * @method WasteMaterial[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<WasteMaterial>
 */
class WasteMaterialRepository extends ServiceEntityRepository
{
    public const ALIAS = 'wwm';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WasteMaterial::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }
}
