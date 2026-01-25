<?php

declare(strict_types=1);

namespace App\Modules\OilService\Controller;

use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Modules\OilService\DTO\ChatDefaultMessageResponseDTO;
use App\Modules\OilService\DTO\ChatMessageListResponseDTO;
use App\Modules\OilService\DTO\ChatMessageRequestDTO;
use App\Modules\OilService\DTO\ChatMessageResponseDTO;
use App\Modules\OilService\DTO\ChatSessionCompleteResponseDTO;
use App\Modules\OilService\DTO\ChatSessionCreateRequestDTO;
use App\Modules\OilService\DTO\ChatSessionCreateResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\OilService\Chat\ChatPromptBuilder;
use App\OilService\Chat\ChatAssistantService;
use App\OilService\Chat\ChatMessageService;
use App\OilService\Chat\ChatSessionService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class ChatController extends AbstractController
{
    private const string FALLBACK_GREETING = 'Dobrý den, mohu vám pomoci s výměnou oleje a filtrů?';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly ChatSessionService $chatSessionService,
        private readonly ChatMessageService $chatMessageService,
        private readonly ChatAssistantService $chatAssistantService,
        private readonly ChatPromptBuilder $chatPromptBuilder,
    ) {
    }

    #[OA\Get(
        tags: [
            'Chat',
        ],
        parameters: [
            new OA\Parameter(
                name: 'language',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'cs-CZ')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Default greeting message',
                content: new Model(
                    type: ChatDefaultMessageResponseDTO::class
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/chat/default-message',
        name: 'oil_service_chat_default_message',
        methods: ['GET']
    )]
    public function defaultMessage(Request $request): JsonResponse
    {
        $language = $request->query->get('language', '');
        $message = $this->chatPromptBuilder->resolveGreeting($language) ?? self::FALLBACK_GREETING;

        $responseDTO = $this->dtoFactory->createChatDefaultMessageResponseDTO(
            $language !== '' ? (string) $language : 'cs-CZ',
            $message,
        );

        return $this->json($responseDTO);
    }

    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: ChatSessionCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Chat',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Chat session created',
                content: new Model(
                    type: ChatSessionCreateResponseDTO::class
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new Model(
                    type: ErrorCollection::class
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/chat/sessions',
        name: 'oil_service_chat_session_create',
        methods: ['POST']
    )]
    public function createSession(Request $request): JsonResponse
    {
        try {
            /** @var ChatSessionCreateRequestDTO $createRequestDTO */
            $createRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                ChatSessionCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($createRequestDTO);

            $session = $this->chatSessionService->createSession($createRequestDTO->getLanguage());

            $greeting = $this->chatPromptBuilder->resolveGreeting($session->getLanguage()) ?? self::FALLBACK_GREETING;
            $this->chatMessageService->addAssistantMessage($session, $greeting);

            $messages = $this->chatMessageService->getMessages($session);
            $responseDTO = $this->dtoFactory->createChatSessionCreateResponseDTO(
                $session,
                $greeting,
                $messages,
            );

            return $this->json($responseDTO);
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection(
                $e->getErrorCollection()
            );
        } catch (InvalidDataException) {
            throw new BadRequestHttpException();
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
    }

    #[OA\Get(
        tags: [
            'Chat',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Chat session messages',
                content: new Model(
                    type: ChatMessageListResponseDTO::class
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Not Found'
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/chat/sessions/{sessionId}',
        name: 'oil_service_chat_session_detail',
        methods: ['GET']
    )]
    public function sessionMessages(string $sessionId): JsonResponse
    {
        try {
            $session = $this->chatSessionService->getSession($sessionId);
            $messages = $this->chatMessageService->getMessages($session);

            $responseDTO = $this->dtoFactory->createChatMessageListResponseDTO(
                $session,
                $messages,
            );

            return $this->json($responseDTO);
        } catch (NotFoundHttpException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
    }

    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: ChatMessageRequestDTO::class
                ),
            )
        ),
        tags: [
            'Chat',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Assistant reply',
                content: new Model(
                    type: ChatMessageResponseDTO::class
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new Model(
                    type: ErrorCollection::class
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Not Found'
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/chat/sessions/{sessionId}/messages',
        name: 'oil_service_chat_message_create',
        methods: ['POST']
    )]
    public function sendMessage(Request $request, string $sessionId): JsonResponse
    {
        try {
            /** @var ChatMessageRequestDTO $messageRequestDTO */
            $messageRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                ChatMessageRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($messageRequestDTO);

            $session = $this->chatSessionService->getSession($sessionId);

            if (!$session->isActive()) {
                throw new BadRequestHttpException('Chat session is not active.');
            }

            $this->chatSessionService->updateSessionLanguage($session, $messageRequestDTO->getLanguage());

            $this->chatMessageService->addUserMessage($session, $messageRequestDTO->getMessage());

            $assistantReply = $this->chatAssistantService->generateAssistantReply($session);
            $assistantMessage = $this->chatMessageService->addAssistantMessage($session, $assistantReply);

            $messages = $this->chatMessageService->getMessages($session);

            $responseDTO = $this->dtoFactory->createChatMessageResponseDTO(
                $session,
                $assistantMessage,
                $messages,
            );

            return $this->json($responseDTO);
        } catch (NotFoundHttpException $e) {
            throw $e;
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection(
                $e->getErrorCollection()
            );
        } catch (InvalidDataException) {
            throw new BadRequestHttpException();
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
    }

    #[OA\Post(
        tags: [
            'Chat',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Chat session completed',
                content: new Model(
                    type: ChatSessionCompleteResponseDTO::class
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Not Found'
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/chat/sessions/{sessionId}/complete',
        name: 'oil_service_chat_session_complete',
        methods: ['POST']
    )]
    public function completeSession(string $sessionId): JsonResponse
    {
        try {
            $session = $this->chatSessionService->getSession($sessionId);
            $this->chatSessionService->completeSession($session);

            $responseDTO = $this->dtoFactory->createChatSessionCompleteResponseDTO();

            return $this->json($responseDTO);
        } catch (NotFoundHttpException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
    }
}
