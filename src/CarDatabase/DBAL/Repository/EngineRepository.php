<?php

declare(strict_types=1);

namespace App\CarDatabase\DBAL\Repository;

use App\CarDatabase\DBAL\Entity\Engine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Engine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Engine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Engine[] findAll()
 * @method Engine[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<Engine>
 */
class EngineRepository extends ServiceEntityRepository
{
    public const ALIAS = 'cde';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Engine::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    public function findOneByEngineCode(string $engineCode): ?Engine
    {
        $qb = $this->getQueryBuilderWithAlias();

        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.engineCode', ':engineCode'))
            ->setParameter('engineCode', $engineCode)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        assert($result instanceof Engine || $result === null);

        return $result;
    }

    public function findOneByEngineCodeAndManufacturer(string $engineCode, string $manufacturer): ?Engine
    {
        $qb = $this->getQueryBuilderWithAlias();

        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.engineCode', ':engineCode'))
            ->andWhere($qb->expr()->eq(self::ALIAS . '.manufacturer', ':manufacturer'))
            ->setParameter('engineCode', $engineCode)
            ->setParameter('manufacturer', $manufacturer)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        assert($result instanceof Engine || $result === null);

        return $result;
    }

    public function findOneByEngineCodeAndManufacturerCaseInsensitive(string $engineCode, string $manufacturer): ?Engine
    {
        $qb = $this->getQueryBuilderWithAlias();

        $qb->andWhere($qb->expr()->eq('LOWER(' . self::ALIAS . '.engineCode)', 'LOWER(:engineCode)'))
            ->andWhere($qb->expr()->eq('LOWER(' . self::ALIAS . '.manufacturer)', 'LOWER(:manufacturer)'))
            ->setParameter('engineCode', $engineCode)
            ->setParameter('manufacturer', $manufacturer)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        assert($result instanceof Engine || $result === null);

        return $result;
    }

    /**
     * @return Engine[]
     */
    public function findMatchingCandidates(
        ?string $manufacturer,
        ?int $displacementCc,
        ?int $powerKw,
        ?string $fuel,
        int $powerToleranceKw = 5,
    ): array {
        $qb = $this->getQueryBuilderWithAlias();

        if ($manufacturer !== null && trim($manufacturer) !== '') {
            $qb->andWhere($qb->expr()->eq('LOWER(' . self::ALIAS . '.manufacturer)', 'LOWER(:manufacturer)'))
                ->setParameter('manufacturer', $manufacturer);
        }

        if ($displacementCc !== null) {
            $qb->andWhere($qb->expr()->eq(self::ALIAS . '.displacementCc', ':displacementCc'))
                ->setParameter('displacementCc', $displacementCc);
        }

        if ($powerKw !== null) {
            $qb->andWhere($qb->expr()->between(self::ALIAS . '.powerKw', ':powerMin', ':powerMax'))
                ->setParameter('powerMin', $powerKw - $powerToleranceKw)
                ->setParameter('powerMax', $powerKw + $powerToleranceKw);
        }

        if ($fuel !== null && trim($fuel) !== '') {
            $qb->andWhere($qb->expr()->eq(self::ALIAS . '.fuel', ':fuel'))
                ->setParameter('fuel', $fuel);
        }

        $result = $qb->getQuery()->getResult();

        return array_values(array_filter($result, static fn (mixed $item): bool => $item instanceof Engine));
    }
}
