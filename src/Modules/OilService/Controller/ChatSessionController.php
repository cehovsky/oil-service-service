<?php

declare(strict_types=1);

namespace App\Modules\OilService\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Core\Helper\IdentParser;
use App\Domain\ApiGrid\ApiGridManager;
use App\Domain\ApiGrid\ApiGridPropertyHelper;
use App\Domain\ApiGrid\OrderEnum;
use App\Domain\Exception\ServerErrorHttpException;
use App\Modules\OilService\DTO\ChatSessionInfoResponseDTO;
use App\Modules\OilService\DTO\ChatSessionListResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\Modules\OilService\Grid\Enum\ChatSessionGridSortEnum;
use App\OilService\DBAL\Entity\ChatSession;
use App\OilService\DBAL\Enum\ChatSessionStatusEnum;
use App\OilService\DBAL\Repository\ChatSessionRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class ChatSessionController extends AbstractController
{
    private const string FILTER_STATUS_KEY = 'status';
    private const string FILTER_ID_KEY = 'id';
    private const string FILTER_IDENT_KEY = 'ident';
    private const string FILTER_LANGUAGE_KEY = 'language';
    private const string FILTER_CREATED_AT_KEY = 'createdAt';
    private const string FILTER_UPDATED_AT_KEY = 'updatedAt';
    private const string FILTER_CLOSED_AT_KEY = 'closedAt';
    private const string FILTER_ORDER_ID_KEY = 'orderId';

    public function __construct(
        private readonly DTOFactory $dtoFactory,
        private readonly ChatSessionRepository $chatSessionRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
    ) {
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'ChatSessions',
        ],
        parameters: [
            new OA\Parameter(
                name: self::FILTER_ID_KEY,
                description: 'Filter by session ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_IDENT_KEY,
                description: 'Filter by session ident (numeric or formatted SYYXXXXX)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_STATUS_KEY,
                description: 'Filter by status',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ChatSessionStatusEnum::VALUES)
            ),
            new OA\Parameter(
                name: self::FILTER_LANGUAGE_KEY,
                description: 'Filter by language',
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
                name: self::FILTER_UPDATED_AT_KEY,
                description: 'Filter by updatedAt (ATOM format)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_CLOSED_AT_KEY,
                description: 'Filter by closedAt (ATOM format)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_ORDER_ID_KEY,
                description: 'Filter by linked order ID',
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
                description: 'Sorting by values, default value createdAt',
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
                content: new Model(type: ChatSessionListResponseDTO::class)
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
        '/oil-service/chat-sessions',
        name: 'oil_service_chat_session_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            try {
                $id = $request->query->get(self::FILTER_ID_KEY);

                assert(is_string($id));

                $qb->andWhere(
                    $qb->expr()->eq(
                        ChatSessionRepository::ALIAS . '.id',
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
                            ChatSessionRepository::ALIAS . '.ident',
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

                $statusEnum = ChatSessionStatusEnum::tryFrom($status);

                if ($statusEnum !== null) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            ChatSessionRepository::ALIAS . '.status',
                            ':status'
                        )
                    );
                    $qb->setParameter('status', $statusEnum->value);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $language = $request->query->get(self::FILTER_LANGUAGE_KEY);

                assert(is_string($language));

                $qb->andWhere(
                    $qb->expr()->eq(
                        ChatSessionRepository::ALIAS . '.language',
                        ':language'
                    )
                );
                $qb->setParameter('language', $language);
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
                            ChatSessionRepository::ALIAS . '.createdAt',
                            ':createdAt'
                        )
                    );
                    $qb->setParameter('createdAt', $createdAt, Types::DATETIME_IMMUTABLE);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $updatedAtRaw = $request->query->get(self::FILTER_UPDATED_AT_KEY);

                assert(is_string($updatedAtRaw));

                $updatedAt = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $updatedAtRaw);

                if ($updatedAt !== false) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            ChatSessionRepository::ALIAS . '.updatedAt',
                            ':updatedAt'
                        )
                    );
                    $qb->setParameter('updatedAt', $updatedAt, Types::DATETIME_IMMUTABLE);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $closedAtRaw = $request->query->get(self::FILTER_CLOSED_AT_KEY);

                assert(is_string($closedAtRaw));

                $closedAt = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $closedAtRaw);

                if ($closedAt !== false) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            ChatSessionRepository::ALIAS . '.closedAt',
                            ':closedAt'
                        )
                    );
                    $qb->setParameter('closedAt', $closedAt, Types::DATETIME_IMMUTABLE);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $orderId = $request->query->get(self::FILTER_ORDER_ID_KEY);

                assert(is_string($orderId));

                if ($orderId !== '') {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            ChatSessionRepository::ALIAS . '.order',
                            ':orderId'
                        )
                    );
                    $qb->setParameter('orderId', $orderId);
                }
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::DESC);
        $sortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            ChatSessionGridSortEnum::class,
            ChatSessionGridSortEnum::CREATED_AT
        );

        $queryBuilder = $this->chatSessionRepository->getQueryBuilderWithAlias();
        $paginator = $this->apiGridManager->createPaginator(
            $queryBuilder,
            $queryModifier,
        );

        /** @var ChatSession[] $sessions */
        $sessions = $this->apiGridManager->fetchData(
            $queryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier,
        );

        $responseDTO = $this->dtoFactory->createChatSessionListResponseDTO(
            $sessions,
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
            'ChatSessions',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detail',
                content: new Model(type: ChatSessionInfoResponseDTO::class)
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
        '/oil-service/chat-sessions/{sessionId}',
        name: 'oil_service_chat_session_info',
        methods: ['GET']
    )]
    public function info(string $sessionId): JsonResponse
    {
        $this->requireAdminUser();

        $session = $this->chatSessionRepository->find($sessionId);

        if ($session === null) {
            throw new NotFoundHttpException();
        }

        $responseDTO = $this->dtoFactory->createChatSessionInfoResponseDTO($session);

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
