<?php

declare(strict_types=1);

namespace App\OilService\Chat;

use App\OilService\DBAL\Entity\ChatKnowledgeItem;
use App\OilService\DBAL\Enum\ChatKnowledgeItemTypeEnum;
use App\OilService\Factory\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;

class ChatKnowledgeService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createItem(
        string $name,
        string $content,
        ChatKnowledgeItemTypeEnum $type,
        string $language,
        bool $isActive,
    ): ChatKnowledgeItem {
        $item = $this->entityFactory->createChatKnowledgeItem(
            $name,
            $content,
            $type,
            $language,
            $isActive,
        );

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return $item;
    }

    public function updateItem(
        ChatKnowledgeItem $item,
        string $name,
        string $content,
        ChatKnowledgeItemTypeEnum $type,
        string $language,
        bool $isActive,
    ): ChatKnowledgeItem {
        $item->setName($name);
        $item->setContent($content);
        $item->setType($type);
        $item->setLanguage($language);
        $item->setIsActive($isActive);

        $this->entityManager->flush();

        return $item;
    }

    public function deleteItem(ChatKnowledgeItem $item): void
    {
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }
}
