<?php

declare(strict_types=1);

namespace App\Modules\OilService\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Core\Helper\IdentParser;
use App\Domain\ApiGrid\ApiGridManager;
use App\Domain\ApiGrid\ApiGridPropertyHelper;
use App\Domain\ApiGrid\OrderEnum;
use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Modules\OilService\DTO\ChatUserRequestInfoResponseDTO;
use App\Modules\OilService\DTO\ChatUserRequestListResponseDTO;
use App\Modules\OilService\DTO\ChatUserRequestDeleteResponseDTO;
use App\Modules\OilService\DTO\ChatUserRequestUpdateRequestDTO;
use App\Modules\OilService\DTO\ChatUserRequestUpdateResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\Modules\OilService\Grid\Enum\ChatUserRequestGridSortEnum;
use App\OilService\Chat\ChatUserRequestService;
use App\OilService\DBAL\Enum\ChatUserRequestStatusEnum;
use App\OilService\DBAL\Entity\ChatUserRequest;
use App\OilService\DBAL\Repository\ChatUserRequestRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
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

class ChatUserRequestController extends AbstractController
{
    private const string FILTER_STATUS_KEY = 'status';
    private const string FILTER_IS_RESOLVED_KEY = 'isResolved';
    private const string FILTER_ID_KEY = 'id';
    private const string FILTER_IDENT_KEY = 'ident';
    private const string FILTER_SESSION_ID_KEY = 'sessionId';
    private const string FILTER_SESSION_IDENT_KEY = 'sessionIdent';
    private const string FILTER_CONTENT_KEY = 'content';
    private const string FILTER_NOTE_KEY = 'note';
    private const string FILTER_LANGUAGE_KEY = 'language';
    private const string FILTER_CREATED_AT_KEY = 'createdAt';
    private const string FILTER_RESOLVED_AT_KEY = 'resolvedAt';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly ChatUserRequestRepository $chatUserRequestRepository,
        private readonly ChatUserRequestService $chatUserRequestService,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'ChatUserRequests',
        ],
        parameters: [
            new OA\Parameter(
                name: self::FILTER_ID_KEY,
                description: 'Filter by request ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_IDENT_KEY,
                description: 'Filter by request ident (numeric or formatted RYYXXXXX)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_STATUS_KEY,
                description: 'Filter by status (open/resolved)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ChatUserRequestStatusEnum::VALUES)
            ),
            new OA\Parameter(
                name: self::FILTER_IS_RESOLVED_KEY,
                description: 'Filter by resolved flag',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean')
            ),
            new OA\Parameter(
                name: self::FILTER_SESSION_ID_KEY,
                description: 'Filter by chat session ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_SESSION_IDENT_KEY,
                description: 'Filter by chat session ident (numeric or formatted SYYXXXXX)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_LANGUAGE_KEY,
                description: 'Filter by session language',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_CONTENT_KEY,
                description: 'Filter by content (contains)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_NOTE_KEY,
                description: 'Filter by note (contains)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_CREATED_AT_KEY,
                description: 'Filter by createdAt (ATOM format)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_RESOLVED_AT_KEY,
                description: 'Filter by resolvedAt (ATOM format)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::PAGE_KEY,
                description: 'Number of page, default value 1',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::MAX_RESULTS_KEY,
                description: 'Number of items on the page, default value '
                    . ApiGridPropertyHelper::MAX_RESULTS_DEFAULT_VALUE,
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: ApiGridPropertyHelper::MAX_RESULTS_DEFAULT_VALUE
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::SORT_KEY,
                description: 'Sorting by values, default isResolved + createdAt',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'createdAt'
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::ORDER_KEY,
                description: 'Select ordering, default value DESC, values: ASC, DESC',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'DESC'
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List',
                content: new Model(type: ChatUserRequestListResponseDTO::class)
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
        '/oil-service/chat-user-requests',
        name: 'oil_service_chat_user_request_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            $qb->leftJoin(ChatUserRequestRepository::ALIAS . '.session', 'session');
            $qb->addSelect('session');

            try {
                $id = $request->query->get(self::FILTER_ID_KEY);

                assert(is_string($id));

                $qb->andWhere(
                    $qb->expr()->eq(
                        ChatUserRequestRepository::ALIAS . '.id',
                        ':id'
                    )
                );
                $qb->setParameter('id', $id);
            } catch (Throwable) {
                // pass
            }

            try {
                $identRaw = $request->query->get(self::FILTER_IDENT_KEY);

                assert(is_string($identRaw));

                $ident = IdentParser::normalizeIdentFilter($identRaw);

                if ($ident !== null) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            ChatUserRequestRepository::ALIAS . '.ident',
                            ':ident'
                        )
                    );
                    $qb->setParameter('ident', $ident);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $status = $request->query->get(self::FILTER_STATUS_KEY);

                assert(is_string($status));

                $statusEnum = ChatUserRequestStatusEnum::tryFrom($status);

                if ($statusEnum !== null) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            ChatUserRequestRepository::ALIAS . '.status',
                            ':status'
                        )
                    );
                    $qb->setParameter('status', $statusEnum->value);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $isResolved = $request->query->get(self::FILTER_IS_RESOLVED_KEY);

                if ($isResolved !== null) {
                    $isResolvedValue = filter_var($isResolved, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                    if ($isResolvedValue !== null) {
                        $qb->andWhere(
                            $qb->expr()->eq(
                                ChatUserRequestRepository::ALIAS . '.isResolved',
                                ':isResolved'
                            )
                        );
                        $qb->setParameter('isResolved', $isResolvedValue);
                    }
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $sessionId = $request->query->get(self::FILTER_SESSION_ID_KEY);

                assert(is_string($sessionId));

                $qb->andWhere(
                    $qb->expr()->eq(
                        ChatUserRequestRepository::ALIAS . '.session',
                        ':sessionId'
                    )
                );
                $qb->setParameter('sessionId', $sessionId);
            } catch (Throwable) {
                // pass
            }

            try {
                $sessionIdentRaw = $request->query->get(self::FILTER_SESSION_IDENT_KEY);

                assert(is_string($sessionIdentRaw));

                $sessionIdent = IdentParser::normalizeIdentFilter($sessionIdentRaw);

                if ($sessionIdent !== null) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            'session.ident',
                            ':sessionIdent'
                        )
                    );
                    $qb->setParameter('sessionIdent', $sessionIdent);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $language = $request->query->get(self::FILTER_LANGUAGE_KEY);

                assert(is_string($language));

                $qb->andWhere(
                    $qb->expr()->eq(
                        'session.language',
                        ':language'
                    )
                );
                $qb->setParameter('language', $language);
            } catch (Throwable) {
                // pass
            }

            try {
                $content = $request->query->get(self::FILTER_CONTENT_KEY);

                assert(is_string($content));

                $qb->andWhere(
                    $qb->expr()->like(
                        ChatUserRequestRepository::ALIAS . '.content',
                        ':content'
                    )
                );
                $qb->setParameter('content', '%' . $content . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $note = $request->query->get(self::FILTER_NOTE_KEY);

                assert(is_string($note));

                $qb->andWhere(
                    $qb->expr()->like(
                        ChatUserRequestRepository::ALIAS . '.note',
                        ':note'
                    )
                );
                $qb->setParameter('note', '%' . $note . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $createdAtRaw = $request->query->get(self::FILTER_CREATED_AT_KEY);

                assert(is_string($createdAtRaw));

                $createdAt = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $createdAtRaw);

                if ($createdAt !== false) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            ChatUserRequestRepository::ALIAS . '.createdAt',
                            ':createdAt'
                        )
                    );
                    $qb->setParameter('createdAt', $createdAt, Types::DATETIME_IMMUTABLE);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $resolvedAtRaw = $request->query->get(self::FILTER_RESOLVED_AT_KEY);

                assert(is_string($resolvedAtRaw));

                $resolvedAt = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $resolvedAtRaw);

                if ($resolvedAt !== false) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            ChatUserRequestRepository::ALIAS . '.resolvedAt',
                            ':resolvedAt'
                        )
                    );
                    $qb->setParameter('resolvedAt', $resolvedAt, Types::DATETIME_IMMUTABLE);
                }
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $hasSort = $request->query->has(ApiGridPropertyHelper::SORT_KEY);

        $queryBuilder = $this->chatUserRequestRepository->getQueryBuilderWithAlias();
        $paginator = $this->apiGridManager->createPaginator(
            $queryBuilder,
            $queryModifier
        );

        if ($hasSort) {
            $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::DESC);
            $sortEnum = $this->apiGridPropertyHelper->createSortEnum(
                $request,
                ChatUserRequestGridSortEnum::class,
                ChatUserRequestGridSortEnum::CREATED_AT
            );

            /** @var ChatUserRequest[] $requests */
            $requests = $this->apiGridManager->fetchData(
                $queryBuilder,
                $sortEnum,
                $orderEnum,
                $firstResult,
                $maxResults,
                $queryModifier
            );
        } else {
            $queryModifier($queryBuilder);
            $queryBuilder->setFirstResult($firstResult);
            $queryBuilder->setMaxResults($maxResults);
            $queryBuilder->orderBy(ChatUserRequestRepository::ALIAS . '.isResolved', 'ASC');
            $queryBuilder->addOrderBy(ChatUserRequestRepository::ALIAS . '.createdAt', 'DESC');

            /** @var ChatUserRequest[] $requests */
            $requests = $queryBuilder->getQuery()->getResult();
        }

        $responseDTO = $this->dtoFactory->createChatUserRequestListResponseDTO(
            $requests,
            $this->apiGridPropertyHelper->createPageCount(
                $paginator->count(),
                $maxResults
            )
        );

        return $this->json($responseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'ChatUserRequests',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detail',
                content: new Model(type: ChatUserRequestInfoResponseDTO::class)
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
        '/oil-service/chat-user-requests/{requestId}',
        name: 'oil_service_chat_user_request_info',
        methods: ['GET']
    )]
    public function info(string $requestId): JsonResponse
    {
        $this->requireAdminUser();

        $requestEntity = $this->chatUserRequestRepository->find($requestId);

        if ($requestEntity === null) {
            throw new NotFoundHttpException();
        }

        $responseDTO = $this->dtoFactory->createChatUserRequestInfoResponseDTO($requestEntity);

        return $this->json($responseDTO);
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
                    type: ChatUserRequestUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'ChatUserRequests',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(type: ChatUserRequestUpdateResponseDTO::class)
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new Model(type: ErrorCollection::class)
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
        '/oil-service/chat-user-requests/{requestId}',
        name: 'oil_service_chat_user_request_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $requestId): JsonResponse
    {
        $this->requireAdminUser();

        $requestEntity = $this->chatUserRequestRepository->find($requestId);

        if ($requestEntity === null) {
            throw new NotFoundHttpException();
        }

        try {
            /** @var ChatUserRequestUpdateRequestDTO $updateRequestDTO */
            $updateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                ChatUserRequestUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($updateRequestDTO);

            $this->chatUserRequestService->updateRequest(
                $requestEntity,
                $updateRequestDTO->getIsResolved(),
                $updateRequestDTO->getNote(),
            );

            $responseDTO = $this->dtoFactory->createChatUserRequestUpdateResponseDTO($requestEntity);

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
            'ChatUserRequests',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(type: ChatUserRequestDeleteResponseDTO::class)
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
        '/oil-service/chat-user-requests/{requestId}',
        name: 'oil_service_chat_user_request_delete',
        methods: ['DELETE']
    )]
    public function delete(string $requestId): JsonResponse
    {
        $this->requireAdminUser();

        $requestEntity = $this->chatUserRequestRepository->find($requestId);

        if ($requestEntity === null) {
            throw new NotFoundHttpException();
        }

        $this->entityManager->remove($requestEntity);
        $this->entityManager->flush();

        $responseDTO = $this->dtoFactory->createChatUserRequestDeleteResponseDTO();

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
