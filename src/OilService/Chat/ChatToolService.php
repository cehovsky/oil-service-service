<?php

declare(strict_types=1);

namespace App\OilService\Chat;

use App\OilService\DBAL\Entity\ChatSession;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Repository\ChatKnowledgeItemRepository;
use App\OilService\DBAL\Repository\PriceListItemRepository;
use App\OilService\DBAL\Repository\TermRepository;
use App\OilService\OrderService;
use App\OilService\Term\TermAvailabilityPolicy;
use RuntimeException;

class ChatToolService
{
    private const string UUID_PATTERN = '/^[0-9a-fA-F-]{36}$/';

    public function __construct(
        private readonly OrderService $orderService,
        private readonly ChatSessionService $chatSessionService,
        private readonly ChatKnowledgeItemRepository $chatKnowledgeItemRepository,
        private readonly PriceListItemRepository $priceListItemRepository,
        private readonly TermRepository $termRepository,
        private readonly ChatUserRequestService $chatUserRequestService,
        private readonly TermAvailabilityPolicy $termAvailabilityPolicy,
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

        $realizationDateInput = isset($payload['realizationDate']) ? (string) $payload['realizationDate'] : '';
        if ($realizationDateInput === '') {
            throw new RuntimeException('Missing realization date.');
        }

        $realizationDate = $this->orderService->createRealizationDate($realizationDateInput);

        $timeSlotValue = isset($payload['realizationTimeSlot']) ? (string) $payload['realizationTimeSlot'] : '';
        $timeSlot = RealizationTimeSlotEnum::tryFrom($timeSlotValue);
        if ($timeSlot === null) {
            throw new RuntimeException('Missing realization time slot.');
        }

        $isAvailable = $this->termRepository->isAvailableTerm(
            $realizationDate,
            $timeSlot,
            $this->termAvailabilityPolicy->getMinimumAvailableDate(),
        );

        if (!$isAvailable) {
            throw new RuntimeException('Selected term is not available.');
        }

        $rawPriceListItems = isset($payload['priceListItemIds']) && is_array($payload['priceListItemIds'])
            ? array_values(array_map('strval', $payload['priceListItemIds']))
            : [];
        $priceListItemIds = $this->normalizePriceListItemIds($rawPriceListItems);

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
            null,
            null,
            null,
            null,
            null,
            [],
            OrderStatusEnum::NEW,
            $timeSlot,
            $realizationDate,
            $priceListItemIds,
            null,
        );

        $session->setOrder($order);
        // Removed automatic session completion - allow agent to offer additional services

        return [
            'sessionId' => $session->getId()->__toString(),
            'sessionIdent' => $session->getFormattedIdent(),
            'orderId' => $order->getId()->__toString(),
            'orderIdent' => $order->getFormattedIdent(),
            'status' => $order->getStatus()->value,
            'realizationDate' => $order->getRealizationDate()->format('Y-m-d'),
            'realizationTimeSlot' => $order->getRealizationTimeSlot()->value,
        ];
    }

    /**
     * @param string[] $rawValues
     *
     * @return string[]
     */
    private function normalizePriceListItemIds(array $rawValues): array
    {
        $normalized = [];
        $labelsOrCodes = [];

        $allowedItems = $this->priceListItemRepository->findActivePublicItemsOrderedByLabel();
        $allowedMap = [];

        foreach ($allowedItems as $item) {
            if ($item->getIsDefault() || $item->getIsHiddenOnInvoice()) {
                continue;
            }

            $id = $item->getId()->__toString();
            $allowedMap[$id] = $id;
            $allowedMap[mb_strtolower($item->getLabel())] = $id;
            $allowedMap[mb_strtolower($item->getCode())] = $id;
        }

        foreach ($rawValues as $value) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                continue;
            }

            if (preg_match(self::UUID_PATTERN, $trimmed) === 1) {
                if (isset($allowedMap[$trimmed])) {
                    $normalized[] = $trimmed;
                }
                continue;
            }

            $labelsOrCodes[] = mb_strtolower($trimmed);
        }

        if ($labelsOrCodes !== []) {
            foreach ($labelsOrCodes as $key) {
                if (isset($allowedMap[$key])) {
                    $normalized[] = $allowedMap[$key];
                }
            }
        }

        return array_values(array_unique($normalized));
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function listAvailableTerms(): array
    {
        $terms = $this->termRepository->findUpcomingAvailableTerms(
            $this->termAvailabilityPolicy->getMinimumAvailableDate()
        );

        return array_map(
            static fn ($term) => [
                'date' => $term->getDate()->format('Y-m-d'),
                'timeSlot' => $term->getTimeSlot()->value,
            ],
            $terms,
        );
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
            'requestIdent' => $request->getFormattedIdent(),
            'status' => $request->getStatus()->value,
        ];
    }
}
