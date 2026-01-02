<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\Form;
use App\OilService\DBAL\Enum\FormStatusEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use DateTimeImmutable;
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

    public function countActiveByDateAndTimeSlot(
        DateTimeImmutable $date,
        RealizationTimeSlotEnum $timeSlot,
    ): int {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->select('COUNT(' . self::ALIAS . '.id)')
            ->andWhere($qb->expr()->eq(self::ALIAS . '.realizationDate', ':realizationDate'))
            ->andWhere($qb->expr()->eq(self::ALIAS . '.realizationTimeSlot', ':realizationTimeSlot'))
            ->andWhere($qb->expr()->neq(self::ALIAS . '.status', ':canceledStatus'))
            ->setParameter('realizationDate', $date)
            ->setParameter('realizationTimeSlot', $timeSlot)
            ->setParameter('canceledStatus', FormStatusEnum::CANCELED->value);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return array<string, int> keyed by "Y-m-d|timeSlot"
     */
    public function getActiveFormCountsByDateRange(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
    ): array {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->select(self::ALIAS . '.realizationDate AS realizationDate')
            ->addSelect(self::ALIAS . '.realizationTimeSlot AS realizationTimeSlot')
            ->addSelect('COUNT(' . self::ALIAS . '.id) AS formCount')
            ->andWhere($qb->expr()->gte(self::ALIAS . '.realizationDate', ':dateFrom'))
            ->andWhere($qb->expr()->lte(self::ALIAS . '.realizationDate', ':dateTo'))
            ->andWhere($qb->expr()->neq(self::ALIAS . '.status', ':canceledStatus'))
            ->groupBy(self::ALIAS . '.realizationDate')
            ->addGroupBy(self::ALIAS . '.realizationTimeSlot')
            ->orderBy(self::ALIAS . '.realizationDate', 'ASC')
            ->addOrderBy(self::ALIAS . '.realizationTimeSlot', 'ASC')
            ->setParameter('dateFrom', $from)
            ->setParameter('dateTo', $to)
            ->setParameter('canceledStatus', FormStatusEnum::CANCELED->value);

        $results = $qb->getQuery()->getArrayResult();

        $counts = [];

        foreach ($results as $row) {
            if (!isset($row['realizationDate'], $row['realizationTimeSlot'], $row['formCount'])) {
                continue;
            }

            $date = $row['realizationDate'];
            $timeSlot = $row['realizationTimeSlot'];
            $count = (int) $row['formCount'];

            if ($date instanceof \DateTimeInterface && is_string($timeSlot)) {
                $key = $date->format('Y-m-d') . '|' . $timeSlot;
                $counts[$key] = $count;
            }
        }

        return $counts;
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
