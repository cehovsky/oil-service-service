<?php

namespace App\Auth\DBAL\Repository;

use App\Auth\DBAL\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public const ALIAS = 'u';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getQueryBuilderWithAlias(): QueryBuilder
    {
        return $this->createQueryBuilder(self::ALIAS);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    /**
     * @return User[]
     */
    public function findUsersByEmailWithNeqId(string $email, Uuid $neqUserId): array
    {
        $qb = $this->createQueryBuilder(self::ALIAS);
        $qb->andWhere($qb->expr()->eq(self::ALIAS . '.email', ':email'));
        $qb->setParameter('email', $email);
        $qb->andWhere($qb->expr()->neq(self::ALIAS . '.id', ':neqUserId'));
        $qb->setParameter('neqUserId', $neqUserId);

        /** @var array<int, User> $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
