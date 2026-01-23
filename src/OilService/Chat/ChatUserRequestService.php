<?php

declare(strict_types=1);

namespace App\OilService\Chat;

use App\OilService\DBAL\Entity\ChatSession;
use App\OilService\DBAL\Entity\ChatUserRequest;
use App\OilService\DBAL\Repository\ChatUserRequestRepository;
use App\OilService\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChatUserRequestService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly ChatUserRequestRepository $chatUserRequestRepository,
    ) {
    }

    public function createRequest(?ChatSession $session, string $content): ChatUserRequest
    {
        if ($session !== null) {
            $existing = $this->chatUserRequestRepository->findOpenBySession($session);

            if ($existing !== null) {
                $existing->updateContent($this->mergeContent($existing->getContent(), $content));
                $this->entityManager->flush();

                return $existing;
            }
        }

        $request = $this->entityFactory->createChatUserRequest($session, $content);

        $this->entityManager->persist($request);
        $this->entityManager->flush();

        return $request;
    }

    public function resolveRequest(string $requestId): ChatUserRequest
    {
        $request = $this->chatUserRequestRepository->find($requestId);

        if ($request === null) {
            throw new NotFoundHttpException();
        }

        if (!$request->getIsResolved()) {
            $request->setIsResolved(true, new DateTimeImmutable());
            $this->entityManager->flush();
        }

        return $request;
    }

    public function updateRequest(ChatUserRequest $request, bool $isResolved, ?string $note): ChatUserRequest
    {
        $request->setIsResolved($isResolved);
        $request->setNote($note);

        $this->entityManager->flush();

        return $request;
    }

    private function mergeContent(string $original, string $newContent): string
    {
        $original = trim($original);
        $newContent = trim($newContent);

        if ($original === '') {
            return $newContent;
        }

        if ($newContent === '') {
            return $original;
        }

        return $original . "\n\n" . $newContent;
    }
}
