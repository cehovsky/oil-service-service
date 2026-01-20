<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\ChatUserRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatUserRequest::class);
    }
}
