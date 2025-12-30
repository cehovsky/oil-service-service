<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\Form;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Form|null find($id, $lockMode = null, $lockVersion = null)
 * @method Form|null findOneBy(array $criteria, array $orderBy = null)
 * @method Form[] findAll()
 * @method Form[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<Form>
 */
class FormRepository extends ServiceEntityRepository
{
    public const ALIAS = 'osf';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Form::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    /**
     * Returns the next available ident number.
     */
    public function getNextIdent(): int
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('MAX(f.ident)');

        $maxIdent = $qb->getQuery()->getSingleScalarResult();

        return $maxIdent !== null ? ((int) $maxIdent) + 1 : 1;
    }

    /**
     * Find forms by ident number or formatted ident (OYYXXXXX format).
     *
     * @return Form[]
     */
    public function findByIdentFilter(string $identFilter): array
    {
        $qb = $this->createQueryBuilder(self::ALIAS);

        // If the filter starts with 'O' or 'o', try to parse it as formatted ident
        if (preg_match('/^[Oo](\d{2})(\d+)$/', $identFilter, $matches)) {
            $identNumber = (int) $matches[2];
            $qb->andWhere($qb->expr()->eq(self::ALIAS . '.ident', ':ident'));
            $qb->setParameter('ident', $identNumber);
        } elseif (is_numeric($identFilter)) {
            // Direct numeric filter
            $qb->andWhere($qb->expr()->eq(self::ALIAS . '.ident', ':ident'));
            $qb->setParameter('ident', (int) $identFilter);
        }

        return $qb->getQuery()->getResult();
    }
}
