<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Repository;

use App\Warehouse\DBAL\Entity\StorageContainerMaterial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StorageContainerMaterial|null find($id, $lockMode = null, $lockVersion = null)
 * @method StorageContainerMaterial|null findOneBy(array $criteria, array $orderBy = null)
 * @method StorageContainerMaterial[] findAll()
 * @method StorageContainerMaterial[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<StorageContainerMaterial>
 */
class StorageContainerMaterialRepository extends ServiceEntityRepository
{
    public const ALIAS = 'wscm';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorageContainerMaterial::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    /**
     * @param string[] $storageContainerIds
     * @return array<string, StorageContainerMaterial[]>
     */
    public function findCurrentByStorageContainerIds(array $storageContainerIds): array
    {
        if ($storageContainerIds === []) {
            return [];
        }

        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->addSelect('storageContainer', 'wasteMaterial');
        $qb->innerJoin(self::ALIAS . '.storageContainer', 'storageContainer');
        $qb->innerJoin(self::ALIAS . '.wasteMaterial', 'wasteMaterial');
        $qb->andWhere($qb->expr()->in('storageContainer.id', ':storageContainerIds'));
        $qb->setParameter('storageContainerIds', $storageContainerIds);
        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.isRecycled', ':isRecycled'));
        $qb->setParameter('isRecycled', false);
        $qb->orderBy(self::ALIAS . '.createdAt', 'ASC');

        /** @var StorageContainerMaterial[] $materials */
        $materials = $qb->getQuery()->getResult();

        $map = [];

        foreach ($materials as $material) {
            $containerId = $material->getStorageContainer()->getId()->__toString();

            if (!array_key_exists($containerId, $map)) {
                $map[$containerId] = [];
            }

            $map[$containerId][] = $material;
        }

        return $map;
    }
}
