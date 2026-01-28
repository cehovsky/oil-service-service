<?php

declare(strict_types=1);

namespace App\Modules\OilService\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Domain\ApiGrid\ApiGridManager;
use App\Domain\ApiGrid\ApiGridPropertyHelper;
use App\Domain\ApiGrid\OrderEnum;
use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Modules\OilService\DTO\TermCreateRequestDTO;
use App\Modules\OilService\DTO\TermCreateResponseDTO;
use App\Modules\OilService\DTO\TermDeleteResponseDTO;
use App\Modules\OilService\DTO\TermInfoResponseDTO;
use App\Modules\OilService\DTO\TermListResponseDTO;
use App\Modules\OilService\DTO\TermUpdateRequestDTO;
use App\Modules\OilService\DTO\TermUpdateResponseDTO;
use App\Modules\OilService\DTO\TermWithOrderCountListResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\Modules\OilService\Grid\Enum\TermGridSortEnum;
use App\Modules\OilService\Validation\Constraint\UniqueTermDateTimeSlot;
use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Repository\OrderRepository;
use App\OilService\DBAL\Repository\TermRepository;
use App\OilService\TermService;
use DateTimeImmutable;
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

class TermController extends AbstractController
{
    private const string FILTER_DATE_KEY = 'date';
    private const string FILTER_TIME_SLOT_KEY = 'timeSlot';
    private const string FILTER_IS_ACTIVE_KEY = 'isActive';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly TermRepository $termRepository,
        private readonly OrderRepository $orderRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly TermService $termService,
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
                    type: TermCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Terms',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: TermCreateResponseDTO::class
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
        '/oil-service/terms',
        name: 'oil_service_term_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $termCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                TermCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($termCreateRequestDTO);

            $term = $this->termService->createTerm(
                new DateTimeImmutable($termCreateRequestDTO->getDate()),
                RealizationTimeSlotEnum::from($termCreateRequestDTO->getTimeSlot()),
                $termCreateRequestDTO->getIsActive(),
                $termCreateRequestDTO->getMaxCount(),
            );

            $termCreateResponseDTO = $this->dtoFactory->createTermCreateResponseDTO($term);

            return $this->json($termCreateResponseDTO);
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
                    type: TermUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Terms',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: TermUpdateResponseDTO::class
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
        '/oil-service/terms/{termId}',
        name: 'oil_service_term_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $termId): JsonResponse
    {
        $this->requireAdminUser();

        $term = $this->termRepository->find($termId);

        if ($term === null) {
            throw new NotFoundHttpException();
        }

        try {
            $termUpdateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                TermUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO(
                $termUpdateRequestDTO,
                new UniqueTermDateTimeSlot(ignoreTermId: $term->getId()->__toString()),
            );

            $this->termService->updateTerm(
                $term,
                new DateTimeImmutable($termUpdateRequestDTO->getDate()),
                RealizationTimeSlotEnum::from($termUpdateRequestDTO->getTimeSlot()),
                $termUpdateRequestDTO->getIsActive(),
                $termUpdateRequestDTO->getMaxCount(),
            );

            $termUpdateResponseDTO = $this->dtoFactory->createTermUpdateResponseDTO($term);

            return $this->json($termUpdateResponseDTO);
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
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Terms',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: TermInfoResponseDTO::class
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
        '/oil-service/terms/{termId}',
        name: 'oil_service_term_info',
        methods: ['GET']
    )]
    public function info(string $termId): JsonResponse
    {
        $this->requireAdminUser();

        $term = $this->termRepository->find($termId);

        if ($term === null) {
            throw new NotFoundHttpException();
        }

        $termInfoResponseDTO = $this->dtoFactory->createTermInfoResponseDTO($term);

        return $this->json($termInfoResponseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Terms',
        ],
        parameters: [
            new OA\Parameter(
                name: self::FILTER_DATE_KEY,
                description: 'strict filtering, date in format YYYY-MM-DD',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: '2025-01-15'
            ),
            new OA\Parameter(
                name: self::FILTER_TIME_SLOT_KEY,
                description: 'strict filtering, enum values: morning, lunch, afternoon',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: RealizationTimeSlotEnum::VALUES
                ),
                example: 'morning'
            ),
            new OA\Parameter(
                name: self::FILTER_IS_ACTIVE_KEY,
                description: 'strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'boolean'
                ),
                example: true
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::PAGE_KEY,
                description: 'Number of page, default value 1',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'integer'
                ),
                example: 1
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::MAX_RESULTS_KEY,
                description: 'Number of items on the page, default value '
                    . ApiGridPropertyHelper::MAX_RESULTS_DEFAULT_VALUE,
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'integer'
                ),
                example: ApiGridPropertyHelper::MAX_RESULTS_DEFAULT_VALUE
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::SORT_KEY,
                description: 'Sorting by values, default value date',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'date'
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::ORDER_KEY,
                description: 'Select ordering, default value DESC, values: ASC, DESC',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'DESC'
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: TermListResponseDTO::class
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/oil-service/terms',
        name: 'oil_service_term_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            try {
                $date = $request->query->get(self::FILTER_DATE_KEY);

                assert(is_string($date));

                $qb->andWhere(
                    $qb->expr()->eq(
                        TermRepository::ALIAS . '.date',
                        ':date'
                    )
                );
                $qb->setParameter('date', new DateTimeImmutable($date));
            } catch (Throwable) {
                // pass
            }

            try {
                $timeSlot = $request->query->get(self::FILTER_TIME_SLOT_KEY);

                assert(is_string($timeSlot));

                $timeSlotEnum = RealizationTimeSlotEnum::from($timeSlot);

                $qb->andWhere(
                    $qb->expr()->eq(
                        TermRepository::ALIAS . '.timeSlot',
                        ':timeSlot'
                    )
                );
                $qb->setParameter('timeSlot', $timeSlotEnum);
            } catch (Throwable) {
                // pass
            }

            try {
                $isActive = $request->query->get(self::FILTER_IS_ACTIVE_KEY);

                if ($isActive === 'true' || $isActive === '1') {
                    $isActiveBool = true;
                } elseif ($isActive === 'false' || $isActive === '0') {
                    $isActiveBool = false;
                } else {
                    throw new InvalidDataException();
                }

                $qb->andWhere(
                    $qb->expr()->eq(
                        TermRepository::ALIAS . '.isActive',
                        ':isActive'
                    )
                );
                $qb->setParameter('isActive', $isActiveBool);
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::DESC);
        $termGridSortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            TermGridSortEnum::class,
            TermGridSortEnum::DATE
        );
        $termsQueryBuilder = $this->termRepository->getQueryBuilderWithAlias();
        $termsPaginator = $this->apiGridManager->createPaginator(
            $termsQueryBuilder,
            $queryModifier
        );
        /** @var Term[] $terms */
        $terms = $this->apiGridManager->fetchData(
            $termsQueryBuilder,
            $termGridSortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );
        $termListResponseDTO = $this->dtoFactory->createTermListResponseDTO(
            $terms,
            $this->apiGridPropertyHelper->createPageCount(
                $termsPaginator->count(),
                $maxResults
            )
        );

        return $this->json($termListResponseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Dashboard',
        ],
        parameters: [
            new OA\Parameter(
                name: 'year',
                description: 'Year of the requested month',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 2025
            ),
            new OA\Parameter(
                name: 'month',
                description: 'Month of the requested year (1-12)',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of terms for a month with order counts',
                content: new Model(
                    type: TermWithOrderCountListResponseDTO::class
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
        '/oil-service/dashboard/terms/monthly',
        name: 'oil_service_dashboard_term_monthly',
        methods: ['GET']
    )]
    public function listByMonth(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $year = $request->query->getInt('year');
        $month = $request->query->getInt('month');

        if ($year <= 0 || $month < 1 || $month > 12) {
            throw new BadRequestHttpException('Invalid month or year.');
        }

        $start = (new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)))->setTime(0, 0);
        $end = $start->modify('last day of this month');

        $terms = $this->termRepository->findByDateRange($start, $end);
        $orderCounts = $this->orderRepository->getActiveOrderCountsByDateRange($start, $end);

        $responseDTO = $this->dtoFactory->createTermWithOrderCountListResponseDTO($terms, $orderCounts);

        return $this->json($responseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Terms',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: TermDeleteResponseDTO::class
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
        '/oil-service/terms/{termId}',
        name: 'oil_service_term_delete',
        methods: ['DELETE']
    )]
    public function delete(string $termId): JsonResponse
    {
        $this->requireAdminUser();

        $term = $this->termRepository->find($termId);

        if ($term === null) {
            throw new NotFoundHttpException();
        }

        $this->termService->deleteTerm($term);

        $termDeleteResponseDTO = $this->dtoFactory->createTermDeleteResponseDTO();

        return $this->json($termDeleteResponseDTO);
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
