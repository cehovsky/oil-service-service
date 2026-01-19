<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\InventoryItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method InventoryItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method InventoryItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method InventoryItem[] findAll()
 * @method InventoryItem[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<InventoryItem>
 */
class InventoryItemRepository extends ServiceEntityRepository
{
    public const ALIAS = 'osi';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InventoryItem::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    /**
     * @param string[] $ids
     *
     * @return InventoryItem[]
     */
    public function findByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $qb = $this->getQueryBuilderWithAlias();

        $qb->andWhere($qb->expr()->in(self::ALIAS . '.id', ':ids'))
            ->setParameter('ids', $ids);

        return $qb->getQuery()->getResult();
    }

    public function findByCode(string $code): ?InventoryItem
    {
        $qb = $this->getQueryBuilderWithAlias();

        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.code', ':code'))
            ->setParameter('code', $code)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getResult();

        return $result[0] ?? null;
    }
}
