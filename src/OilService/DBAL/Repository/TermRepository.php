<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\Term;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Term|null find($id, $lockMode = null, $lockVersion = null)
 * @method Term|null findOneBy(array $criteria, array $orderBy = null)
 * @method Term[] findAll()
 * @method Term[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<Term>
 */
class TermRepository extends ServiceEntityRepository
{
    public const ALIAS = 'ost';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Term::class);
    }
}
