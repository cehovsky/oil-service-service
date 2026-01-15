<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\PriceListItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PriceListItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceListItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceListItem[] findAll()
 * @method PriceListItem[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<PriceListItem>
 */
class PriceListItemRepository extends ServiceEntityRepository
{
    public const ALIAS = 'ospli';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceListItem::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    public function findByCode(string $code): ?PriceListItem
    {
        return $this->findOneBy([
            'code' => $code,
        ]);
    }

    /**
     * @param string[] $ids
     *
     * @return PriceListItem[]
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

    /**
     * @return PriceListItem[]
     */
    public function findDefaultActiveItems(): array
    {
        $qb = $this->getQueryBuilderWithAlias();

        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.isDefault', ':isDefault'))
            ->andWhere($qb->expr()->eq(self::ALIAS . '.isActive', ':isActive'))
            ->setParameter('isDefault', true)
            ->setParameter('isActive', true)
            ->orderBy(self::ALIAS . '.label', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return PriceListItem[]
     */
    public function findActivePublicItemsOrderedByLabel(): array
    {
        $qb = $this->getQueryBuilderWithAlias();

        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.isActive', ':isActive'))
            ->andWhere($qb->expr()->eq(self::ALIAS . '.isPublic', ':isPublic'))
            ->setParameter('isActive', true)
            ->setParameter('isPublic', true)
            ->orderBy(self::ALIAS . '.label', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string[] $ids
     *
     * @return PriceListItem[]
     */
    public function findActiveVisibleNonDefaultByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $qb = $this->getQueryBuilderWithAlias();

        $qb->andWhere($qb->expr()->in(self::ALIAS . '.id', ':ids'))
            ->andWhere($qb->expr()->eq(self::ALIAS . '.isActive', ':isActive'))
            ->andWhere($qb->expr()->eq(self::ALIAS . '.isHiddenOnInvoice', ':isHiddenOnInvoice'))
            ->andWhere($qb->expr()->eq(self::ALIAS . '.isDefault', ':isDefault'))
            ->setParameter('ids', $ids)
            ->setParameter('isActive', true)
            ->setParameter('isHiddenOnInvoice', false)
            ->setParameter('isDefault', false);

        return $qb->getQuery()->getResult();
    }
}
