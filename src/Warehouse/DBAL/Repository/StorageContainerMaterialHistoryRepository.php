<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Repository;

use App\Warehouse\DBAL\Entity\StorageContainerMaterialHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StorageContainerMaterialHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method StorageContainerMaterialHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method StorageContainerMaterialHistory[] findAll()
 * @method StorageContainerMaterialHistory[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<StorageContainerMaterialHistory>
 */
class StorageContainerMaterialHistoryRepository extends ServiceEntityRepository
{
    public const ALIAS = 'wscmh';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorageContainerMaterialHistory::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }
}
