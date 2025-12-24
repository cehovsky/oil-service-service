<?php

declare(strict_types=1);

namespace App\Auth\DBAL\Repository;

use App\Auth\DBAL\Entity\AccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AccessToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessToken[]    findAll()
 * @method AccessToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<AccessToken>
 */
class AccessTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessToken::class);
    }

    public function findByToken(string $token): ?AccessToken
    {
        $qb = $this->createQueryBuilder('ta');
        $qb->join('ta.refreshToken', 'tr');
        $qb->join('tr.user', 'u');
        $qb->addSelect('tr');
        $qb->addSelect('u');
        $qb->where($qb->expr()->eq('ta.token', ':token'));
        $qb->setParameter('token', $token);

        try {
            /** @var AccessToken|null $result */
            $result = $qb->getQuery()->getOneOrNullResult();

            return $result;
        } catch (NonUniqueResultException) {
            return null;
        }
    }
}
