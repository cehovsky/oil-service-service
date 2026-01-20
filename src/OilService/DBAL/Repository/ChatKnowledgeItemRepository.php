<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Repository;

use App\OilService\DBAL\Entity\ChatKnowledgeItem;
use App\OilService\DBAL\Enum\ChatKnowledgeItemTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatKnowledgeItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatKnowledgeItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatKnowledgeItem[] findAll()
 * @method ChatKnowledgeItem[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @template-extends ServiceEntityRepository<ChatKnowledgeItem>
 */
class ChatKnowledgeItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatKnowledgeItem::class);
    }

    /**
     * @return ChatKnowledgeItem[]
     */
    public function findActiveKnowledgeByLanguage(string $language): array
    {
        return $this->findBy([
            'language' => $language,
            'type' => ChatKnowledgeItemTypeEnum::KNOWLEDGE,
            'isActive' => true,
        ], [
            'createdAt' => 'ASC',
        ]);
    }

    public function findActiveGreetingByLanguage(string $language): ?ChatKnowledgeItem
    {
        return $this->findOneBy([
            'language' => $language,
            'type' => ChatKnowledgeItemTypeEnum::GREETING,
            'isActive' => true,
        ], [
            'createdAt' => 'DESC',
        ]);
    }

    /**
     * @return ChatKnowledgeItem[]
     */
    public function findActiveByLanguage(string $language): array
    {
        return $this->findBy([
            'language' => $language,
            'isActive' => true,
        ], [
            'createdAt' => 'ASC',
        ]);
    }
}
