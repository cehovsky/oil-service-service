<?php

declare(strict_types=1);

namespace App\OilService\Chat;

use App\OilService\DBAL\Entity\ChatSession;
use App\OilService\DBAL\Enum\ChatMessageRoleEnum;
use App\OilService\DBAL\Repository\ChatMessageRepository;
use App\OilService\Chat\ChatToolService;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;
use stdClass;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ChatAssistantService
{
    private const string DEFAULT_MODEL = 'gpt-5-nano';
    private const string EMPTY_RESPONSE_FALLBACK = 'Sorry, I could not generate a response. Please try again.';

    private string $model;
    private string $apiKey;
    private ?string $organization;
    private ?string $project;

    public function __construct(
        private readonly ChatPromptBuilder $chatPromptBuilder,
        private readonly ChatMessageRepository $chatMessageRepository,
        private readonly ChatToolService $chatToolService,
        private readonly LoggerInterface $logger,
        private readonly HttpClientInterface $httpClient,
        #[Autowire('%env(OPENAI_API_KEY)%')] string $apiKey,
        #[Autowire('%env(default::OPENAI_CHAT_MODEL)%')] ?string $model = null,
        #[Autowire('%env(default::OPENAI_ORGANIZATION)%')] ?string $organization = null,
        #[Autowire('%env(default::OPENAI_PROJECT)%')] ?string $project = null,
    ) {
        $this->apiKey = $apiKey;
        $this->organization = $organization !== null && $organization !== '' ? $organization : null;
        $this->project = $project !== null && $project !== '' ? $project : null;
        $this->model = $model !== null && $model !== '' ? $model : self::DEFAULT_MODEL;
    }

    public function generateAssistantReply(ChatSession $session, ?callable $onTextDelta = null): string
    {
        $messages = $this->buildMessages($session);
        $inputItems = $this->mapMessagesToInputItems($messages);
        $tools = $this->buildResponseTools();

        for ($i = 0; $i < 5; $i++) {
            $responseData = $this->createResponse($inputItems, $tools, $i + 1, true, $onTextDelta);
            $toolCalls = $this->extractFunctionCalls($responseData);

            if ($toolCalls === []) {
                $reply = $this->extractResponseText($responseData);

                if ($reply !== '') {
                    return $reply;
                }

                break;
            }

            $inputItems = $this->appendResponseOutput($inputItems, $responseData);

            foreach ($toolCalls as $toolCall) {
                try {
                    $toolResult = $this->executeToolByName($session, $toolCall['name'], $toolCall['arguments']);
                } catch (Throwable $e) {
                    $this->logger->warning('Chat tool execution failed', [
                    'sessionId' => $session->getId()->__toString(),
                    'toolName' => $toolCall['name'],
                    'message' => $e->getMessage(),
                    ]);

                    $toolResult = [
                    'error' => $e->getMessage(),
                    ];
                }

                $inputItems[] = [
                'type' => 'function_call_output',
                'call_id' => $toolCall['call_id'],
                'output' => json_encode($toolResult, JSON_THROW_ON_ERROR),
                ];
            }
        }

        // Fallback attempt without tools if the model returned no content and no tool calls.
        $plainResponseData = $this->createResponse($inputItems, null, null, false, $onTextDelta);
        $plainContent = $this->extractResponseText($plainResponseData);

        if ($plainContent !== '') {
            return $plainContent;
        }

        return self::EMPTY_RESPONSE_FALLBACK;
    }

    /**
     * @param array<int, array<string, mixed>> $messages
     *
     * @return array<int, array<string, mixed>>
     */
    private function mapMessagesToInputItems(array $messages): array
    {
        return array_map(
            static fn (array $message) => [
            'role' => $message['role'],
            'content' => $message['content'],
            ],
            $messages,
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildResponseTools(): array
    {
        return [
        [
            'type' => 'function',
            'name' => 'submit_order',
            'description' => 'Ulož objednávku (vytvoř Order) z již získaných dat.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'fullName' => ['type' => 'string'],
                    'phone' => ['type' => 'string'],
                    'email' => ['type' => 'string'],
                    'carModel' => ['type' => 'string'],
                    'licensePlate' => ['type' => 'string'],
                    'vin' => ['type' => 'string', 'description' => 'Optional VIN'],
                    'address' => ['type' => 'string'],
                    'note' => ['type' => 'string'],
                    'isCompany' => ['type' => 'boolean'],
                    'companyName' => ['type' => 'string'],
                    'companyIdentificationNumber' => ['type' => 'string'],
                    'companyTaxId' => ['type' => 'string'],
                    'companyAddress' => ['type' => 'string'],
                    'realizationDate' => [
                        'type' => 'string',
                        'description' => 'Preferovaný formát pro komunikaci je j. n. Y (např. 5. 3. 2026), '
                            . 'ale akceptován je i YYYY-MM-DD a další běžné formáty data.',
                    ],
                    'realizationTimeSlot' => ['type' => 'string', 'enum' => RealizationTimeSlotEnum::VALUES],
                    'priceListItemIds' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Doplňkové služby podle ID, kódu nebo názvu. Pokud nic nepřidává, nech prázdné.'],
                ],
                'required' => ['fullName', 'phone', 'email', 'carModel', 'licensePlate', 'address', 'realizationDate', 'realizationTimeSlot'],
            ],
        ],
        [
            'type' => 'function',
            'name' => 'validate_service_address',
            'description' => 'Ověří adresu: zda je rozpoznaná a zda spadá do servisní oblasti.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'address' => ['type' => 'string'],
                ],
                'required' => ['address'],
            ],
        ],
        [
            'type' => 'function',
            'name' => 'fetch_knowledge',
            'description' => 'Dohledá konkrétní knowledge položky podle názvů a vrátí obsah v aktuálním jazyce.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'names' => [
                        'type' => 'array',
                        'items' => ['type' => 'string'],
                    ],
                ],
                'required' => ['names'],
            ],
        ],
        [
            'type' => 'function',
            'name' => 'list_available_terms',
            'description' => 'Vrátí seznam dostupných termínů podle stejné logiky jako /oil-service/terms/available.',
            'parameters' => [
                'type' => 'object',
                'properties' => new stdClass(),
            ],
        ],
        [
            'type' => 'function',
            'name' => 'complete_session',
            'description' => 'Ukonči chat session, pokud je objednávka hotová nebo se uživatel rozloučil.',
            'parameters' => [
                'type' => 'object',
                'properties' => new stdClass(),
            ],
        ],
        [
            'type' => 'function',
            'name' => 'store_user_request',
            'description' => 'Ulož požadavek/zprávu, kterou nelze zpracovat (např. prosba o kontakt nebo nestandardní vzkaz).',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'content' => ['type' => 'string'],
                ],
                'required' => ['content'],
            ],
        ],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $inputItems
     * @param array<int, array<string, mixed>>|null $tools
     * @param int|null $attempt
     * @param callable(string):void|null $onTextDelta
     * @return array<string, mixed>
     */
    private function createResponse(array $inputItems, ?array $tools, ?int $attempt, bool $withTools, ?callable $onTextDelta = null): array
    {
        $payload = [
        'model' => $this->model,
        'input' => $inputItems,
        'max_output_tokens' => 600,
        'reasoning' => [
            'effort' => 'low',
        ],
        'text' => [
            'format' => [
                'type' => 'text',
            ],
            'verbosity' => 'low',
        ],
        ];

        if ($withTools && $tools !== null) {
            $payload['tools'] = $tools;
        }

        $this->logger->debug('OpenAI responses request', [
        'attempt' => $attempt,
        'withTools' => $withTools,
        'payload' => $payload,
        ]);

        $headers = [
        'Authorization' => 'Bearer ' . $this->apiKey,
        'Content-Type' => 'application/json',
        ];

        if ($this->organization !== null) {
            $headers['OpenAI-Organization'] = $this->organization;
        }

        if ($this->project !== null) {
            $headers['OpenAI-Project'] = $this->project;
        }

        if ($onTextDelta !== null) {
            $payload['stream'] = true;

            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/responses', [
            'headers' => $headers,
            'json' => $payload,
            'buffer' => false,
            ]);

            $responseData = $this->consumeStreamedResponse($response, $onTextDelta);
        } else {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/responses', [
            'headers' => $headers,
            'json' => $payload,
            ]);

            $responseData = $response->toArray(false);
        }

        $this->logger->debug('OpenAI responses response', [
        'attempt' => $attempt,
        'withTools' => $withTools,
        'statusCode' => $response->getStatusCode(),
        'response' => $responseData,
        ]);

        return $responseData;
    }

    /**
     * @param callable(string):void $onTextDelta
     * @return array<string, mixed>
     */
    private function consumeStreamedResponse(ResponseInterface $response, callable $onTextDelta): array
    {
        $buffer = '';
        $completedResponse = null;

        foreach ($this->httpClient->stream($response) as $chunk) {
            if ($chunk->isTimeout() || $chunk->isFirst()) {
                continue;
            }

            if ($chunk->isLast()) {
                break;
            }

            $buffer .= $chunk->getContent();

            while (($separatorPosition = strpos($buffer, "\n\n")) !== false) {
                $rawEvent = substr($buffer, 0, $separatorPosition);
                $buffer = substr($buffer, $separatorPosition + 2);

                $eventPayload = $this->parseServerSentEvent($rawEvent);

                if ($eventPayload === null) {
                    continue;
                }

                if (($eventPayload['type'] ?? '') === 'response.output_text.delta') {
                    $delta = (string) ($eventPayload['delta'] ?? '');

                    if ($delta !== '') {
                        $onTextDelta($delta);
                    }

                    continue;
                }

                if (($eventPayload['type'] ?? '') === 'response.completed') {
                    $responseData = $eventPayload['response'] ?? null;

                    if (is_array($responseData)) {
                        $completedResponse = $responseData;
                    }

                    continue;
                }

                if (($eventPayload['type'] ?? '') === 'error') {
                    $message = (string) ($eventPayload['message'] ?? 'OpenAI streamed response failed.');
                    throw new RuntimeException($message);
                }
            }
        }

        if (!is_array($completedResponse)) {
            throw new RuntimeException('OpenAI streamed response did not contain completed payload.');
        }

        return $completedResponse;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseServerSentEvent(string $rawEvent): ?array
    {
        $dataLines = [];

        $lines = preg_split('/\r?\n/', $rawEvent);

        if ($lines === false) {
            return null;
        }

        foreach ($lines as $line) {
            if (str_starts_with($line, 'data:')) {
                $dataLines[] = ltrim(substr($line, 5));
            }
        }

        if ($dataLines === []) {
            return null;
        }

        $rawData = implode("\n", $dataLines);

        if ($rawData === '[DONE]') {
            return null;
        }

        $decoded = json_decode($rawData, true);

        if (!is_array($decoded)) {
            return null;
        }

        return $decoded;
    }

    /**
     * @param array<string, mixed> $responseData
     *
     * @return array<int, array{call_id: string, name: string, arguments: array<string, mixed>}>
     */
    private function extractFunctionCalls(array $responseData): array
    {
        $output = $responseData['output'] ?? [];
        if (!is_array($output)) {
            return [];
        }

        $calls = [];

        foreach ($output as $item) {
            if (!is_array($item) || ($item['type'] ?? null) !== 'function_call') {
                continue;
            }

            $name = (string) ($item['name'] ?? '');
            $callId = (string) ($item['call_id'] ?? '');
            $rawArguments = (string) ($item['arguments'] ?? '{}');
            $decoded = json_decode($rawArguments, true);

            if (!is_array($decoded)) {
                $decoded = [];
            }

            if ($name === '' || $callId === '') {
                continue;
            }

            $calls[] = [
            'call_id' => $callId,
            'name' => $name,
            'arguments' => $decoded,
            ];
        }

        return $calls;
    }

    /**
     * @param array<string, mixed> $responseData
     */
    private function extractResponseText(array $responseData): string
    {
        $output = $responseData['output'] ?? [];
        if (!is_array($output)) {
            return '';
        }

        $chunks = [];

        foreach ($output as $item) {
            if (!is_array($item) || ($item['type'] ?? null) !== 'message') {
                continue;
            }

            $content = $item['content'] ?? [];
            if (!is_array($content)) {
                continue;
            }

            foreach ($content as $contentItem) {
                if (!is_array($contentItem) || ($contentItem['type'] ?? null) !== 'output_text') {
                    continue;
                }

                $text = (string) ($contentItem['text'] ?? '');
                if ($text !== '') {
                    $chunks[] = $text;
                }
            }
        }

        return trim(implode('', $chunks));
    }

    /**
     * @param array<int, array<string, mixed>> $inputItems
     * @param array<string, mixed> $responseData
     *
     * @return array<int, array<string, mixed>>
     */
    private function appendResponseOutput(array $inputItems, array $responseData): array
    {
        $output = $responseData['output'] ?? [];
        if (!is_array($output)) {
            return $inputItems;
        }

        foreach ($output as $item) {
            if (is_array($item)) {
                $inputItems[] = $item;
            }
        }

        return $inputItems;
    }

    /**
     * @param array<string, mixed> $arguments
     *
     * @return mixed
     */
    private function executeToolByName(ChatSession $session, string $name, array $arguments): mixed
    {
        if ($name === 'submit_order') {
            return $this->chatToolService->submitOrder($session, $arguments);
        }

        if ($name === 'fetch_knowledge') {
            $names = $arguments['names'] ?? [];
            if (!is_array($names)) {
                $names = [];
            }
            return $this->chatToolService->fetchKnowledge($session, $names);
        }

        if ($name === 'validate_service_address') {
            $address = (string) ($arguments['address'] ?? '');
            if ($address === '') {
                throw new RuntimeException('Missing address for validation.');
            }

            return $this->chatToolService->validateServiceAddress($session, $address);
        }

        if ($name === 'complete_session') {
            return $this->chatToolService->completeSession($session);
        }

        if ($name === 'list_available_terms') {
            return [
            'terms' => $this->chatToolService->listAvailableTerms(),
            ];
        }

        if ($name === 'store_user_request') {
            $content = (string) ($arguments['content'] ?? '');
            if ($content === '') {
                throw new RuntimeException('Missing content for user request.');
            }

            return $this->chatToolService->storeUserRequest($session, $content);
        }

        throw new RuntimeException('Unknown tool: ' . $name);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildMessages(ChatSession $session): array
    {
        $messages = [
        [
            'role' => 'system',
            'content' => $this->chatPromptBuilder->buildSystemPrompt($session->getLanguage()),
        ],
        ];

        $sessionStateMessage = $this->buildSessionStateMessage($session);

        if ($sessionStateMessage !== '') {
            $messages[] = [
            'role' => 'system',
            'content' => $sessionStateMessage,
            ];
        }

        foreach ($this->chatMessageRepository->findBySession($session) as $message) {
            $role = $message->getRole();

            if ($role === ChatMessageRoleEnum::USER || $role === ChatMessageRoleEnum::ASSISTANT) {
                $messages[] = [
                'role' => $role->value,
                'content' => $message->getContent(),
                ];
            }
        }

        return $messages;
    }

    private function buildSessionStateMessage(ChatSession $session): string
    {
        $stateParts = [];

        if ($session->getOrder() !== null) {
            $stateParts[] = sprintf(
                'SESSION_STATE: Order already exists in this session (ID %s). Never create a second order '
                    . 'for this session; if submit_order is called again, treat it as an update of the existing order.',
                $session->getOrder()->getFormattedIdent(),
            );
        }

        if ($session->getValidatedServiceAddressRecognized() !== null && $session->getValidatedServiceAddress() !== null) {
            $stateParts[] = sprintf(
                'SESSION_STATE: Last validated address is "%s" (normalized: "%s"); recognized=%s; withinServiceArea=%s. '
                    . 'Do not ask for address validation again unless the customer explicitly changes the address.',
                $session->getValidatedServiceAddress(),
                $session->getValidatedServiceAddressNormalized() ?? '',
                $session->getValidatedServiceAddressRecognized() ? 'true' : 'false',
                $session->getValidatedServiceAddressWithinServiceArea() === null
                    ? 'unknown'
                    : ($session->getValidatedServiceAddressWithinServiceArea() ? 'true' : 'false'),
            );
        }

        return implode("\n", $stateParts);
    }
}
