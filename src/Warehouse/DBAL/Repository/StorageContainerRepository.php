<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Repository;

use App\Warehouse\DBAL\Entity\StorageContainer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StorageContainer|null find($id, $lockMode = null, $lockVersion = null)
 * @method StorageContainer|null findOneBy(array $criteria, array $orderBy = null)
 * @method StorageContainer[] findAll()
 * @method StorageContainer[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<StorageContainer>
 */
class StorageContainerRepository extends ServiceEntityRepository
{
    public const ALIAS = 'wsc';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StorageContainer::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    public function findByCode(string $code): ?StorageContainer
    {
        return $this->findOneBy(['code' => $code]);
    }
}
