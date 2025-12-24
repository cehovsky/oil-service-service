<?php

declare(strict_types=1);

namespace App\Auth\DBAL\Repository;

use App\Auth\DBAL\Entity\RefreshToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RefreshToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method RefreshToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method RefreshToken[]    findAll()
 * @method RefreshToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<RefreshToken>
 */
class RefreshTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function findByToken(string $token): ?RefreshToken
    {
        $qb = $this->createQueryBuilder('tr');
        $qb->join('tr.user', 'u');
        $qb->addSelect('u');
        $qb->where($qb->expr()->eq('tr.token', ':token'));
        $qb->setParameter('token', $token);

        try {
            /** @var RefreshToken|null $result */
            $result = $qb->getQuery()->getOneOrNullResult();

            return $result;
        } catch (NonUniqueResultException) {
            return null;
        }
    }
}
