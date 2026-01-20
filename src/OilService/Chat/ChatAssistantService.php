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
use stdClass;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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

    public function generateAssistantReply(ChatSession $session): string
    {
        $messages = $this->buildMessages($session);
        $inputItems = $this->mapMessagesToInputItems($messages);
        $tools = $this->buildResponseTools();

        for ($i = 0; $i < 5; $i++) {
            $responseData = $this->createResponse($inputItems, $tools, $i + 1, true);
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
                $toolResult = $this->executeToolByName($session, $toolCall['name'], $toolCall['arguments']);
                $inputItems[] = [
                    'type' => 'function_call_output',
                    'call_id' => $toolCall['call_id'],
                    'output' => json_encode($toolResult, JSON_THROW_ON_ERROR),
                ];
            }
        }

        // Fallback attempt without tools if the model returned no content and no tool calls.
        $plainResponseData = $this->createResponse($inputItems, null, null, false);
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
                        'address' => ['type' => 'string'],
                        'note' => ['type' => 'string'],
                        'isCompany' => ['type' => 'boolean'],
                        'companyName' => ['type' => 'string'],
                        'companyIdentificationNumber' => ['type' => 'string'],
                        'companyTaxId' => ['type' => 'string'],
                        'companyAddress' => ['type' => 'string'],
                        'realizationDate' => ['type' => 'string', 'description' => 'YYYY-MM-DD'],
                        'realizationTimeSlot' => ['type' => 'string', 'enum' => RealizationTimeSlotEnum::VALUES],
                        'priceListItemIds' => ['type' => 'array', 'items' => ['type' => 'string']],
                    ],
                    'required' => ['fullName', 'phone', 'email', 'carModel', 'licensePlate', 'address'],
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
     */
    private function createResponse(array $inputItems, ?array $tools, ?int $attempt, bool $withTools): array
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

        $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/responses', [
            'headers' => $headers,
            'json' => $payload,
        ]);

        $responseData = $response->toArray(false);

        $this->logger->debug('OpenAI responses response', [
            'attempt' => $attempt,
            'withTools' => $withTools,
            'statusCode' => $response->getStatusCode(),
            'response' => $responseData,
        ]);

        return is_array($responseData) ? $responseData : [];
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
            $decoded = json_decode($rawArguments, true) ?? [];

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
     * @return array<string, mixed>
     */
    private function executeToolByName(ChatSession $session, string $name, array $arguments): array
    {
        if ($name === 'submit_order') {
            return $this->chatToolService->submitOrder($session, $arguments);
        }

        if ($name === 'fetch_knowledge') {
            return $this->chatToolService->fetchKnowledge($session, $arguments['names'] ?? []);
        }

        if ($name === 'complete_session') {
            return $this->chatToolService->completeSession($session);
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

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildTools(): array
    {
        return [
            [
                'type' => 'function',
                'function' => [
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
                            'address' => ['type' => 'string'],
                            'note' => ['type' => 'string'],
                            'isCompany' => ['type' => 'boolean'],
                            'companyName' => ['type' => 'string'],
                            'companyIdentificationNumber' => ['type' => 'string'],
                            'companyTaxId' => ['type' => 'string'],
                            'companyAddress' => ['type' => 'string'],
                            'realizationDate' => ['type' => 'string', 'description' => 'YYYY-MM-DD'],
                            'realizationTimeSlot' => ['type' => 'string', 'enum' => RealizationTimeSlotEnum::VALUES],
                            'priceListItemIds' => ['type' => 'array', 'items' => ['type' => 'string']],
                        ],
                        'required' => ['fullName', 'phone', 'email', 'carModel', 'licensePlate', 'address'],
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
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
            ],
            [
                'type' => 'function',
                'function' => [
                    'name' => 'complete_session',
                    'description' => 'Ukonči chat session, pokud je objednávka hotová nebo se uživatel rozloučil.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => new stdClass(),
                    ],
                ],
            ],
            [
                'type' => 'function',
                'function' => [
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
            ],
        ];
    }

    /**
     * @param object $toolCall
     *
     * @return array<string, mixed>
     */
    private function convertAssistantToolCallMessage(object $toolCallMessage): array
    {
        $toolCalls = $toolCallMessage->toolCalls ?? $toolCallMessage->tool_calls ?? [];

        return [
            'role' => 'assistant',
            'content' => $toolCallMessage->content,
            'tool_calls' => array_map(
                static fn ($toolCall) => [
                    'id' => $toolCall->id,
                    'type' => $toolCall->type,
                    'function' => [
                        'name' => $toolCall->function->name,
                        'arguments' => $toolCall->function->arguments,
                    ],
                ],
                $toolCalls,
            ),
        ];
    }

    /**
     * @param object $toolCall
     *
     * @return array<string, mixed>
     */
    private function executeToolAndConvertResult(ChatSession $session, object $toolCall): array
    {
        $name = $toolCall->function->name ?? '';
        $rawArguments = $toolCall->function->arguments ?? '{}';
        $decoded = json_decode($rawArguments, true) ?? [];

        $result = [];

        if ($name === 'submit_order') {
            $result = $this->chatToolService->submitOrder($session, $decoded);
        } elseif ($name === 'fetch_knowledge') {
            $result = $this->chatToolService->fetchKnowledge($session, $decoded['names'] ?? []);
        } elseif ($name === 'complete_session') {
            $result = $this->chatToolService->completeSession($session);
        } elseif ($name === 'store_user_request') {
            $content = (string) ($decoded['content'] ?? '');
            if ($content === '') {
                throw new RuntimeException('Missing content for user request.');
            }
            $result = $this->chatToolService->storeUserRequest($session, $content);
        } else {
            throw new RuntimeException('Unknown tool: ' . $name);
        }

        return [
            'role' => 'tool',
            'tool_call_id' => $toolCall->id,
            'name' => $name,
            'content' => json_encode($result, JSON_THROW_ON_ERROR),
        ];
    }
}
