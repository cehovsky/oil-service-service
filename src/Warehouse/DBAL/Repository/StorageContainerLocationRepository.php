<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Repository;

use App\Warehouse\DBAL\Entity\StorageContainerLocation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StorageContainerLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method StorageContainerLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method StorageContainerLocation[] findAll()
 * @method StorageContainerLocation[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<StorageContainerLocation>
 */
class StorageContainerLocationRepository extends ServiceEntityRepository
{
    public const ALIAS = 'wscl';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorageContainerLocation::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }
}
