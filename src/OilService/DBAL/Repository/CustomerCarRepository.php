<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\CustomerCar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CustomerCar|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerCar|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerCar[] findAll()
 * @method CustomerCar[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<CustomerCar>
 */
class CustomerCarRepository extends ServiceEntityRepository
{
    public const ALIAS = 'oscc';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerCar::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    public function findOneByLicensePlate(string $licensePlate): ?CustomerCar
    {
        return $this->findOneBy([
            'licensePlate' => $licensePlate,
        ]);
    }

    public function findOneByVin(string $vin): ?CustomerCar
    {
        return $this->findOneBy([
            'vin' => $vin,
        ]);
    }
}
