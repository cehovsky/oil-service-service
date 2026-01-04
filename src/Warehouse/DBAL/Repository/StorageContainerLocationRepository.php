<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Repository;

use App\Warehouse\DBAL\Entity\StorageContainerLocation;
use App\Warehouse\DBAL\Entity\Warehouse;
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

    /**
     * @param string[] $storageContainerIds
     * @return array<string, StorageContainerLocation>
     */
    public function findLatestByStorageContainerIds(array $storageContainerIds): array
    {
        if ($storageContainerIds === []) {
            return [];
        }

        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->andWhere(
            $qb->expr()->in(self::ALIAS . '.storageContainer', ':storageContainerIds')
        );
        $qb->setParameter('storageContainerIds', $storageContainerIds);
        $qb->andWhere(
            $qb->expr()->eq(
                self::ALIAS . '.movedAt',
                '(SELECT MAX(scl2.movedAt) FROM ' . StorageContainerLocation::class . ' scl2 '
                . 'WHERE scl2.storageContainer = ' . self::ALIAS . '.storageContainer)'
            )
        );

        /** @var StorageContainerLocation[] $locations */
        $locations = $qb->getQuery()->getResult();

        $map = [];

        foreach ($locations as $location) {
            $map[$location->getStorageContainer()->getId()->__toString()] = $location;
        }

        return $map;
    }

    /**
     * @return StorageContainerLocation[]
     */
    public function findLatestForWarehouse(Warehouse $warehouse): array
    {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->andWhere(
            $qb->expr()->eq(self::ALIAS . '.warehouse', ':warehouse')
        );
        $qb->setParameter('warehouse', $warehouse);
        $qb->andWhere(
            $qb->expr()->eq(
                self::ALIAS . '.movedAt',
                '(SELECT MAX(scl2.movedAt) FROM ' . StorageContainerLocation::class . ' scl2 '
                . 'WHERE scl2.storageContainer = ' . self::ALIAS . '.storageContainer)'
            )
        );
        $qb->orderBy(self::ALIAS . '.movedAt', 'DESC');

        /** @var StorageContainerLocation[] $locations */
        return $qb->getQuery()->getResult();
    }
}
