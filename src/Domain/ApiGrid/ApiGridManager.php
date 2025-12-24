<?php

namespace App\Domain\ApiGrid;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ApiGridManager
{
    /**
     * @return Paginator<mixed>
     */
    public function createPaginator(
        QueryBuilder $queryBuilder,
        ?callable $queryModifier = null
    ): Paginator {
        $clonedQueryBuilder = clone $queryBuilder;

        $this->applyQueryModification($clonedQueryBuilder, $queryModifier);

        return new Paginator($clonedQueryBuilder);
    }

    public function fetchData(
        QueryBuilder $queryBuilder,
        OrderEnumInterface $sort,
        OrderEnum $order = OrderEnum::ASC,
        int $firstResult = 0,
        int $maxResults = 100,
        ?callable $queryModifier = null
    ): mixed {
        $clonedQueryBuilder = clone $queryBuilder;

        $this->applyQueryModification($clonedQueryBuilder, $queryModifier);

        $clonedQueryBuilder->setFirstResult($firstResult);
        $clonedQueryBuilder->setMaxResults($maxResults);
        $clonedQueryBuilder->orderBy($sort->toSql(), $order->value);

        return $clonedQueryBuilder->getQuery()->getResult();
    }

    private function applyQueryModification(
        QueryBuilder $queryBuilder,
        ?callable $queryModifier = null
    ): void {
        if (is_callable($queryModifier)) {
            $queryModifier($queryBuilder);
        }
    }
}
