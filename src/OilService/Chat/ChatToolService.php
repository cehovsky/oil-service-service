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
use App\OilService\ServiceArea\ServiceAreaAddressEvaluationResult;
use App\OilService\ServiceArea\ServiceAreaAddressEvaluationService;
use App\OilService\Term\TermAvailabilityPolicy;
use App\Domain\Exception\ValidationException;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;

class ChatToolService
{
    private const string UUID_PATTERN = '/^[0-9a-fA-F-]{36}$/';
    private const int MAX_USER_REQUEST_CONTENT_LENGTH = 2000;

    public function __construct(
        private readonly OrderService $orderService,
        private readonly ChatSessionService $chatSessionService,
        private readonly ChatKnowledgeItemRepository $chatKnowledgeItemRepository,
        private readonly PriceListItemRepository $priceListItemRepository,
        private readonly TermRepository $termRepository,
        private readonly ChatUserRequestService $chatUserRequestService,
        private readonly TermAvailabilityPolicy $termAvailabilityPolicy,
        private readonly ServiceAreaAddressEvaluationService $addressEvaluationService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function submitOrder(ChatSession $session, array $payload): array
    {
        $existingOrder = $session->getOrder();

        $fullName = trim((string) ($payload['fullName'] ?? $existingOrder?->getFullName() ?? ''));
        $phone = trim((string) ($payload['phone'] ?? $existingOrder?->getPhone() ?? ''));
        $email = trim((string) ($payload['email'] ?? $existingOrder?->getEmail() ?? ''));
        $carModel = trim((string) ($payload['carModel'] ?? $existingOrder?->getCarModel() ?? ''));
        $licensePlate = trim((string) ($payload['licensePlate'] ?? $existingOrder?->getLicensePlate() ?? ''));
        $vinRaw = isset($payload['vin'])
            ? trim((string) $payload['vin'])
            : trim((string) ($existingOrder?->getVin() ?? ''));
        $vin = $vinRaw !== '' ? $vinRaw : null;
        $address = trim((string) ($payload['address'] ?? $existingOrder?->getAddress() ?? ''));
        $normalizedAddress = $this->normalizeAddress($address);

        if ($fullName === '' || $phone === '' || $email === '' || $carModel === '' || $licensePlate === '' || $address === '') {
            throw new RuntimeException('Missing required order fields.');
        }

        $note = isset($payload['note']) ? (string) $payload['note'] : $existingOrder?->getNote();
        $isCompany = isset($payload['isCompany']) ? (bool) $payload['isCompany'] : ($existingOrder?->getIsCompany() ?? false);
        $companyName = isset($payload['companyName']) ? (string) $payload['companyName'] : $existingOrder?->getCompanyName();
        $companyIdentificationNumber = isset($payload['companyIdentificationNumber']) ? (string) $payload['companyIdentificationNumber'] : $existingOrder?->getCompanyIdentificationNumber();
        $companyTaxId = isset($payload['companyTaxId']) ? (string) $payload['companyTaxId'] : $existingOrder?->getCompanyTaxId();
        $companyAddress = isset($payload['companyAddress']) ? (string) $payload['companyAddress'] : $existingOrder?->getCompanyAddress();

        $existingRealizationDate = $existingOrder?->getRealizationDate();
        $realizationDateInput = isset($payload['realizationDate'])
            ? (string) $payload['realizationDate']
            : ($existingRealizationDate?->format('Y-m-d') ?? '');
        if ($realizationDateInput === '') {
            throw new RuntimeException('Missing realization date.');
        }

        $realizationDate = $this->orderService->createRealizationDate($realizationDateInput);

        $existingTimeSlot = $existingOrder?->getRealizationTimeSlot();
        $timeSlotValue = isset($payload['realizationTimeSlot'])
            ? (string) $payload['realizationTimeSlot']
            : ($existingTimeSlot?->value ?? '');
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

        $rawPriceListItems = [];
        if (isset($payload['priceListItemIds']) && is_array($payload['priceListItemIds'])) {
            $rawPriceListItems = array_values(array_map('strval', $payload['priceListItemIds']));
        } elseif ($existingOrder !== null) {
            foreach ($existingOrder->getPriceListItems() as $priceListItem) {
                if ($priceListItem->getIsDefault() || $priceListItem->getIsHiddenOnInvoice()) {
                    continue;
                }

                $rawPriceListItems[] = $priceListItem->getId()->__toString();
            }
        }
        $priceListItemIds = $this->normalizePriceListItemIds($rawPriceListItems);

        $cachedAddressEvaluation = $this->resolveCachedAddressEvaluation($session, $normalizedAddress);

        try {
            $order = $this->orderService->upsertChatSessionOrderWithUser(
                $session->getOrder(),
                $fullName,
                $phone,
                $email,
                $carModel,
                $licensePlate,
                $vin,
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
                $cachedAddressEvaluation,
            );
        } catch (ValidationException) {
            throw new RuntimeException('Address is not recognizable. Ask the customer to provide a more precise address.');
        }

        $session->setOrder($order);
        $this->entityManager->flush();
        // Removed automatic session completion - allow agent to offer additional services

        return [
            'sessionId' => $session->getId()->__toString(),
            'sessionIdent' => $session->getFormattedIdent(),
            'orderId' => $order->getId()->__toString(),
            'orderIdent' => $order->getFormattedIdent(),
            'status' => $order->getStatus()->value,
            'realizationDate' => $order->getRealizationDate()->format('Y-m-d'),
            'realizationTimeSlot' => $order->getRealizationTimeSlot()->value,
            'latitude' => $order->getLatitude(),
            'longitude' => $order->getLongitude(),
            'isWithinServiceArea' => $order->getIsWithinServiceArea(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function validateServiceAddress(ChatSession $session, string $address): array
    {
        $evaluation = $this->addressEvaluationService->evaluateAddress($address);

        $session->setValidatedServiceAddressState(
            $address,
            $this->normalizeAddress($address),
            $evaluation->isRecognized(),
            $evaluation->getWithinServiceArea(),
            $evaluation->getLatitude(),
            $evaluation->getLongitude(),
            new DateTimeImmutable(),
        );
        $this->entityManager->flush();

        return [
            'isRecognized' => $evaluation->isRecognized(),
            'isWithinServiceArea' => $evaluation->getWithinServiceArea(),
            'latitude' => $evaluation->getLatitude(),
            'longitude' => $evaluation->getLongitude(),
            'message' => $evaluation->getMessage(),
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
        $normalizedContent = $this->sanitizeUserRequestContent($content);
        if ($normalizedContent === '') {
            throw new RuntimeException('Missing content for user request.');
        }

        $request = $this->chatUserRequestService->createRequest($session, $normalizedContent);

        return [
            'requestId' => $request->getId()->__toString(),
            'requestIdent' => $request->getFormattedIdent(),
            'status' => $request->getStatus()->value,
        ];
    }

    private function normalizeAddress(string $address): string
    {
        $singleSpaced = preg_replace('/\s+/u', ' ', trim($address));

        return mb_strtolower($singleSpaced ?? '');
    }

    private function resolveCachedAddressEvaluation(
        ChatSession $session,
        string $normalizedAddress,
    ): ?ServiceAreaAddressEvaluationResult {
        if ($normalizedAddress === '') {
            return null;
        }

        if ($session->getValidatedServiceAddressNormalized() !== $normalizedAddress) {
            return null;
        }

        if ($session->getValidatedServiceAddressRecognized() !== true) {
            return null;
        }

        $latitude = $session->getValidatedServiceAddressLatitude();
        $longitude = $session->getValidatedServiceAddressLongitude();
        $withinServiceArea = $session->getValidatedServiceAddressWithinServiceArea();

        if ($latitude === null || $longitude === null || $withinServiceArea === null) {
            return null;
        }

        return ServiceAreaAddressEvaluationResult::recognized($latitude, $longitude, $withinServiceArea);
    }

    private function sanitizeUserRequestContent(string $content): string
    {
        $trimmed = trim($content);
        $collapsedWhitespace = preg_replace('/\s+/u', ' ', $trimmed);
        $normalized = $collapsedWhitespace !== null ? $collapsedWhitespace : $trimmed;

        return mb_substr($normalized, 0, self::MAX_USER_REQUEST_CONTENT_LENGTH);
    }
}
