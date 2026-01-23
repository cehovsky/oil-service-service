<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\ChatSession;
use App\OilService\DBAL\Entity\ChatUserRequest;
use App\OilService\DBAL\Enum\ChatUserRequestStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatUserRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatUserRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatUserRequest[] findAll()
 * @method ChatUserRequest[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<ChatUserRequest>
 */
class ChatUserRequestRepository extends ServiceEntityRepository
{
    public const ALIAS = 'oscur';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatUserRequest::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    public function getNextIdent(): int
    {
        $qb = $this->createQueryBuilder('cur');
        $qb->select('MAX(cur.ident)');

        $maxIdent = $qb->getQuery()->getSingleScalarResult();

        return $maxIdent !== null ? ((int) $maxIdent) + 1 : 1;
    }

    public function findOpenBySession(ChatSession $session): ?ChatUserRequest
    {
        $qb = $this->createQueryBuilder(self::ALIAS);

        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.session', ':session'))
            ->andWhere($qb->expr()->eq(self::ALIAS . '.status', ':status'))
            ->setParameter('session', $session)
            ->setParameter('status', ChatUserRequestStatusEnum::OPEN->value)
            ->orderBy(self::ALIAS . '.createdAt', 'DESC')
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
