<?php

declare(strict_types=1);

namespace App\OilService\Chat;

use App\OilService\DBAL\Entity\ChatSession;
use App\OilService\DBAL\Entity\ChatUserRequest;
use App\OilService\DBAL\Enum\ChatUserRequestStatusEnum;
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

        if ($request->getStatus() !== ChatUserRequestStatusEnum::RESOLVED) {
            $request->markResolved(new DateTimeImmutable());
            $this->entityManager->flush();
        }

        return $request;
    }
}
