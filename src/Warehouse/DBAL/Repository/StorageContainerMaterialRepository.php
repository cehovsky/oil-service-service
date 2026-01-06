<?php

declare(strict_types=1);

namespace App\Warehouse\DBAL\Repository;

use App\Warehouse\DBAL\Entity\StorageContainerMaterial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}
