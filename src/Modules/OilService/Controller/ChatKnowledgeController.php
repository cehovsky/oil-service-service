<?php

declare(strict_types=1);

namespace App\Modules\OilService\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Core\Helper\QueryParameterParser;
use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\OilService\Chat\ChatKnowledgeService;
use App\Modules\OilService\DTO\ChatKnowledgeItemCreateRequestDTO;
use App\Modules\OilService\DTO\ChatKnowledgeItemCreateResponseDTO;
use App\Modules\OilService\DTO\ChatKnowledgeItemDeleteResponseDTO;
use App\Modules\OilService\DTO\ChatKnowledgeItemInfoResponseDTO;
use App\Modules\OilService\DTO\ChatKnowledgeItemListResponseDTO;
use App\Modules\OilService\DTO\ChatKnowledgeItemUpdateRequestDTO;
use App\Modules\OilService\DTO\ChatKnowledgeItemUpdateResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\OilService\DBAL\Enum\ChatKnowledgeItemTypeEnum;
use App\OilService\DBAL\Repository\ChatKnowledgeItemRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;
use ValueError;

class ChatKnowledgeController extends AbstractController
{
    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly ChatKnowledgeItemRepository $chatKnowledgeItemRepository,
        private readonly ChatKnowledgeService $chatKnowledgeService,
        private readonly Security $security,
    ) {
    }

    #[OA\Post(
        security: [
            [
                'Bearer' => []
            ],
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: ChatKnowledgeItemCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'ChatKnowledge',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: ChatKnowledgeItemCreateResponseDTO::class
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
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden'
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/oil-service/chat/knowledge',
        name: 'oil_service_chat_knowledge_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            /** @var ChatKnowledgeItemCreateRequestDTO $createRequestDTO */
            $createRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                ChatKnowledgeItemCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($createRequestDTO);

            $item = $this->chatKnowledgeService->createItem(
                $createRequestDTO->getName(),
                $createRequestDTO->getContent(),
                ChatKnowledgeItemTypeEnum::from($createRequestDTO->getType()),
                $createRequestDTO->getLanguage(),
                $createRequestDTO->getIsActive(),
            );

            $responseDTO = $this->dtoFactory->createChatKnowledgeItemCreateResponseDTO($item);

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

    #[OA\Put(
        security: [
            [
                'Bearer' => []
            ],
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: ChatKnowledgeItemUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'ChatKnowledge',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: ChatKnowledgeItemUpdateResponseDTO::class
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
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden'
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
        '/oil-service/chat/knowledge/{itemId}',
        name: 'oil_service_chat_knowledge_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $itemId): JsonResponse
    {
        $this->requireAdminUser();

        $item = $this->chatKnowledgeItemRepository->find($itemId);

        if ($item === null) {
            throw new NotFoundHttpException();
        }

        try {
            /** @var ChatKnowledgeItemUpdateRequestDTO $updateRequestDTO */
            $updateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                ChatKnowledgeItemUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($updateRequestDTO);

            $item = $this->chatKnowledgeService->updateItem(
                $item,
                $updateRequestDTO->getName(),
                $updateRequestDTO->getContent(),
                ChatKnowledgeItemTypeEnum::from($updateRequestDTO->getType()),
                $updateRequestDTO->getLanguage(),
                $updateRequestDTO->getIsActive(),
            );

            $responseDTO = $this->dtoFactory->createChatKnowledgeItemUpdateResponseDTO($item);

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

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'ChatKnowledge',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: ChatKnowledgeItemDeleteResponseDTO::class
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden'
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
        '/oil-service/chat/knowledge/{itemId}',
        name: 'oil_service_chat_knowledge_delete',
        methods: ['DELETE']
    )]
    public function delete(string $itemId): JsonResponse
    {
        $this->requireAdminUser();

        $item = $this->chatKnowledgeItemRepository->find($itemId);

        if ($item === null) {
            throw new NotFoundHttpException();
        }

        $this->chatKnowledgeService->deleteItem($item);

        $responseDTO = $this->dtoFactory->createChatKnowledgeItemDeleteResponseDTO();

        return $this->json($responseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        parameters: [
            new OA\Parameter(
                name: 'language',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'cs-CZ')
            ),
            new OA\Parameter(
                name: 'type',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ChatKnowledgeItemTypeEnum::VALUES)
            ),
        ],
        tags: [
            'ChatKnowledge',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List',
                content: new Model(
                    type: ChatKnowledgeItemListResponseDTO::class
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden'
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/oil-service/chat/knowledge',
        name: 'oil_service_chat_knowledge_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $criteria = [];
        $language = $request->query->get('language');
        $type = $request->query->get('type');

        if (is_string($language) && $language !== '') {
            $criteria['language'] = $language;
        }

        if (is_string($type) && $type !== '') {
            $criteria['type'] = QueryParameterParser::parseEnum($type, ChatKnowledgeItemTypeEnum::class);
        }

        $items = $criteria === []
            ? $this->chatKnowledgeItemRepository->findAll()
            : $this->chatKnowledgeItemRepository->findBy($criteria, ['createdAt' => 'DESC']);

        $responseDTO = $this->dtoFactory->createChatKnowledgeItemListResponseDTO($items);

        return $this->json($responseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'ChatKnowledge',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detail',
                content: new Model(
                    type: ChatKnowledgeItemInfoResponseDTO::class
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized'
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden'
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
        '/oil-service/chat/knowledge/{itemId}',
        name: 'oil_service_chat_knowledge_info',
        methods: ['GET']
    )]
    public function info(string $itemId): JsonResponse
    {
        $this->requireAdminUser();

        $item = $this->chatKnowledgeItemRepository->find($itemId);

        if ($item === null) {
            throw new NotFoundHttpException();
        }

        $responseDTO = $this->dtoFactory->createChatKnowledgeItemInfoResponseDTO($item);

        return $this->json($responseDTO);
    }

    private function requireAdminUser(): AuthUser
    {
        $user = $this->security->getUser();

        if (!$user instanceof AuthUser) {
            throw new ServerErrorHttpException();
        }

        if (!$user->getIsAdmin() && !$user->getIsOffice()) {
            throw new AccessDeniedHttpException();
        }

        return $user;
    }
}
