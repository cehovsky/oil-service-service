<?php

declare(strict_types=1);

namespace App\OilService\Chat;

use App\OilService\DBAL\Entity\ChatSession;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Repository\ChatKnowledgeItemRepository;
use App\OilService\DBAL\Repository\PriceListItemRepository;
use App\OilService\OrderService;
use DateTimeImmutable;
use RuntimeException;

class ChatToolService
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly ChatSessionService $chatSessionService,
        private readonly ChatKnowledgeItemRepository $chatKnowledgeItemRepository,
        private readonly PriceListItemRepository $priceListItemRepository,
        private readonly ChatUserRequestService $chatUserRequestService,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function submitOrder(ChatSession $session, array $payload): array
    {
        $fullName = (string) ($payload['fullName'] ?? '');
        $phone = (string) ($payload['phone'] ?? '');
        $email = (string) ($payload['email'] ?? '');
        $carModel = (string) ($payload['carModel'] ?? '');
        $licensePlate = (string) ($payload['licensePlate'] ?? '');
        $address = (string) ($payload['address'] ?? '');

        if ($fullName === '' || $phone === '' || $email === '' || $carModel === '' || $licensePlate === '' || $address === '') {
            throw new RuntimeException('Missing required order fields.');
        }

        $note = isset($payload['note']) ? (string) $payload['note'] : null;
        $isCompany = isset($payload['isCompany']) ? (bool) $payload['isCompany'] : false;
        $companyName = isset($payload['companyName']) ? (string) $payload['companyName'] : null;
        $companyIdentificationNumber = isset($payload['companyIdentificationNumber']) ? (string) $payload['companyIdentificationNumber'] : null;
        $companyTaxId = isset($payload['companyTaxId']) ? (string) $payload['companyTaxId'] : null;
        $companyAddress = isset($payload['companyAddress']) ? (string) $payload['companyAddress'] : null;

        $realizationDateInput = isset($payload['realizationDate']) ? (string) $payload['realizationDate'] : null;
        $realizationDate = $realizationDateInput ? $this->orderService->createRealizationDate($realizationDateInput) : new DateTimeImmutable('tomorrow');

        $timeSlotValue = isset($payload['realizationTimeSlot']) ? (string) $payload['realizationTimeSlot'] : RealizationTimeSlotEnum::MORNING->value;
        $timeSlot = RealizationTimeSlotEnum::tryFrom($timeSlotValue) ?? RealizationTimeSlotEnum::MORNING;

        $priceListItemIds = isset($payload['priceListItemIds']) && is_array($payload['priceListItemIds'])
            ? array_values(array_map('strval', $payload['priceListItemIds']))
            : [];

        $order = $this->orderService->createOrderWithUser(
            $fullName,
            $phone,
            $email,
            $carModel,
            $licensePlate,
            $address,
            $note,
            $isCompany,
            $companyName,
            $companyIdentificationNumber,
            $companyTaxId,
            $companyAddress,
            OrderStatusEnum::NEW,
            $timeSlot,
            $realizationDate,
            $priceListItemIds,
            null,
        );

        return [
            'orderId' => $order->getId()->__toString(),
            'orderIdent' => $order->getFormattedIdent(),
            'status' => $order->getStatus()->value,
            'realizationDate' => $order->getRealizationDate()->format('Y-m-d'),
            'realizationTimeSlot' => $order->getRealizationTimeSlot()->value,
        ];
    }

    /**
     * @param array<string> $names
     *
     * @return array<int, array<string, string>>
     */
    public function fetchKnowledge(ChatSession $session, array $names): array
    {
        $language = $session->getLanguage();
        $items = $this->chatKnowledgeItemRepository->findBy([
            'language' => $language,
            'name' => $names,
            'isActive' => true,
        ]);

        return array_map(
            static fn ($item) => [
                'name' => $item->getName(),
                'content' => $item->getContent(),
                'type' => $item->getType()->value,
            ],
            $items,
        );
    }

    /**
     * @return array<string, string>
     */
    public function completeSession(ChatSession $session): array
    {
        $this->chatSessionService->completeSession($session);

        return [
            'sessionId' => $session->getId()->__toString(),
            'status' => $session->getStatus()->value,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function storeUserRequest(ChatSession $session, string $content): array
    {
        $request = $this->chatUserRequestService->createRequest($session, $content);

        return [
            'requestId' => $request->getId()->__toString(),
            'status' => $request->getStatus()->value,
        ];
    }
}
