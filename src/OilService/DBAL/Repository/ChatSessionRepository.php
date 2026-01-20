<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\ChatSession;
use App\OilService\DBAL\Enum\ChatSessionStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatSession[] findAll()
 * @method ChatSession[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<ChatSession>
 */
class ChatSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatSession::class);
    }

    public function findActive(string $sessionId): ?ChatSession
    {
        return $this->findOneBy([
            'id' => $sessionId,
            'status' => ChatSessionStatusEnum::ACTIVE,
        ]);
    }
}
