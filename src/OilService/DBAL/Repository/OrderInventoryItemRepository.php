<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\OrderInventoryItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method OrderInventoryItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderInventoryItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderInventoryItem[] findAll()
 * @method OrderInventoryItem[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<OrderInventoryItem>
 */
class OrderInventoryItemRepository extends ServiceEntityRepository
{
    public const ALIAS = 'osoii';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderInventoryItem::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }
}
