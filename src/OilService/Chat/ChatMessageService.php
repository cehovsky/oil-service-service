<?php

declare(strict_types=1);

namespace App\OilService\Chat;

use App\OilService\DBAL\Entity\ChatMessage;
use App\OilService\DBAL\Entity\ChatSession;
use App\OilService\DBAL\Enum\ChatMessageRoleEnum;
use App\OilService\DBAL\Repository\ChatMessageRepository;
use App\OilService\Factory\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;

class ChatMessageService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly ChatMessageRepository $chatMessageRepository,
    ) {
    }

    public function addUserMessage(ChatSession $session, string $content): ChatMessage
    {
        return $this->addMessage($session, ChatMessageRoleEnum::USER, $content);
    }

    public function addAssistantMessage(ChatSession $session, string $content): ChatMessage
    {
        return $this->addMessage($session, ChatMessageRoleEnum::ASSISTANT, $content);
    }

    public function addSystemMessage(ChatSession $session, string $content): ChatMessage
    {
        return $this->addMessage($session, ChatMessageRoleEnum::SYSTEM, $content);
    }

    public function addNoteMessage(ChatSession $session, string $content): ChatMessage
    {
        return $this->addMessage($session, ChatMessageRoleEnum::NOTE, $content);
    }

    /**
     * @return ChatMessage[]
     */
    public function getMessages(ChatSession $session): array
    {
        return $this->chatMessageRepository->findBySession($session);
    }

    private function addMessage(ChatSession $session, ChatMessageRoleEnum $role, string $content): ChatMessage
    {
        $message = $this->entityFactory->createChatMessage($session, $role, $content);
        $session->addMessage($message);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }
}
