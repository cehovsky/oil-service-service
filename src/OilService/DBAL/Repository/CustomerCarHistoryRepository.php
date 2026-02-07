<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\CustomerCarHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CustomerCarHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerCarHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerCarHistory[] findAll()
 * @method CustomerCarHistory[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<CustomerCarHistory>
 */
class CustomerCarHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerCarHistory::class);
    }
}
