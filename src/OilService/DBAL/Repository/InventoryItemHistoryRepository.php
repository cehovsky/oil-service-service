<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\InventoryItemHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InventoryItemHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method InventoryItemHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method InventoryItemHistory[] findAll()
 * @method InventoryItemHistory[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<InventoryItemHistory>
 */
class InventoryItemHistoryRepository extends ServiceEntityRepository
{
    public const ALIAS = 'osih';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InventoryItemHistory::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }
}
