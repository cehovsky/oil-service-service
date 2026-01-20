<?php

declare(strict_types=1);

namespace App\OilService\Chat;

use App\OilService\DBAL\Entity\ChatSession;
use App\OilService\DBAL\Enum\ChatSessionStatusEnum;
use App\OilService\DBAL\Repository\ChatSessionRepository;
use App\OilService\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChatSessionService
{
    private const string DEFAULT_LANGUAGE = 'cs-CZ';

    public function __construct(
        private readonly ChatSessionRepository $chatSessionRepository,
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createSession(?string $language = null): ChatSession
    {
        $normalizedLanguage = $this->normalizeLanguage($language);
        $session = $this->entityFactory->createChatSession(
            $normalizedLanguage,
            ChatSessionStatusEnum::ACTIVE,
        );

        $this->entityManager->persist($session);
        $this->entityManager->flush();

        return $session;
    }

    public function getSession(string $sessionId): ChatSession
    {
        $session = $this->chatSessionRepository->find($sessionId);

        if ($session === null) {
            throw new NotFoundHttpException();
        }

        return $session;
    }

    public function completeSession(ChatSession $session): ChatSession
    {
        if ($session->getStatus() === ChatSessionStatusEnum::COMPLETED) {
            return $session;
        }

        $session->markCompleted(new DateTimeImmutable());
        $this->entityManager->flush();

        return $session;
    }

    public function updateSessionLanguage(ChatSession $session, ?string $language): ChatSession
    {
        if ($language === null || $language === '') {
            return $session;
        }

        $session->setLanguage($language);
        $this->entityManager->flush();

        return $session;
    }

    private function normalizeLanguage(?string $language): string
    {
        if ($language === null || $language === '') {
            return self::DEFAULT_LANGUAGE;
        }

        return $language;
    }
}
