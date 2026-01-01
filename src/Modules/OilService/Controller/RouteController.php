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
use App\Modules\OilService\DTO\RouteCreateRequestDTO;
use App\Modules\OilService\DTO\RouteCreateResponseDTO;
use App\Modules\OilService\DTO\RouteDeleteResponseDTO;
use App\Modules\OilService\DTO\RouteInfoResponseDTO;
use App\Modules\OilService\DTO\RouteListResponseDTO;
use App\Modules\OilService\DTO\RouteUpdateRequestDTO;
use App\Modules\OilService\DTO\RouteUpdateResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\Modules\OilService\Grid\Enum\RouteGridSortEnum;
use App\OilService\DBAL\Entity\Route as RouteEntity;
use App\OilService\DBAL\Repository\RouteRepository;
use App\OilService\RouteService;
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

class RouteController extends AbstractController
{
    private const string FILTER_DATE_KEY = 'date';
    private const string FILTER_IS_ACTIVE_KEY = 'isActive';
    private const string FILTER_CAR_ID_KEY = 'carId';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly RouteRepository $routeRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly RouteService $routeService,
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
                    type: RouteCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'OilService',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: RouteCreateResponseDTO::class
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
        '/oil-service/routes',
        name: 'oil_service_route_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $routeCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                RouteCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($routeCreateRequestDTO);

            $route = $this->routeService->createRoute(
                $routeCreateRequestDTO->getCarId(),
                $routeCreateRequestDTO->getIsActive(),
                new DateTimeImmutable($routeCreateRequestDTO->getDate()),
                $routeCreateRequestDTO->getTermIds(),
            );

            $routeCreateResponseDTO = $this->dtoFactory->createRouteCreateResponseDTO($route);

            return $this->json($routeCreateResponseDTO);
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
                    type: RouteUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'OilService',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: RouteUpdateResponseDTO::class
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
        '/oil-service/routes/{routeId}',
        name: 'oil_service_route_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $routeId): JsonResponse
    {
        $this->requireAdminUser();

        $route = $this->routeRepository->find($routeId);

        if ($route === null) {
            throw new NotFoundHttpException('Route not found');
        }

        try {
            $routeUpdateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                RouteUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($routeUpdateRequestDTO);

            $route = $this->routeService->updateRoute(
                $route,
                $routeUpdateRequestDTO->getCarId(),
                $routeUpdateRequestDTO->getIsActive(),
                new DateTimeImmutable($routeUpdateRequestDTO->getDate()),
                $routeUpdateRequestDTO->getTermIds(),
            );

            $routeUpdateResponseDTO = $this->dtoFactory->createRouteUpdateResponseDTO($route);

            return $this->json($routeUpdateResponseDTO);
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
            'OilService',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: RouteInfoResponseDTO::class
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
        '/oil-service/routes/{routeId}',
        name: 'oil_service_route_info',
        methods: ['GET']
    )]
    public function info(string $routeId): JsonResponse
    {
        $this->requireAdminUser();

        $route = $this->routeRepository->find($routeId);

        if ($route === null) {
            throw new NotFoundHttpException('Route not found');
        }

        $routeInfoResponseDTO = $this->dtoFactory->createRouteInfoResponseDTO($route);

        return $this->json($routeInfoResponseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'OilService',
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
                name: self::FILTER_CAR_ID_KEY,
                description: 'strict filtering, UUID of the car',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'a1b2c3d4-e5f6-7890-abcd-ef1234567890'
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
                    type: RouteListResponseDTO::class
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
        '/oil-service/routes',
        name: 'oil_service_route_list',
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
                        RouteRepository::ALIAS . '.date',
                        ':date'
                    )
                );
                $qb->setParameter('date', new DateTimeImmutable($date));
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
                        RouteRepository::ALIAS . '.isActive',
                        ':isActive'
                    )
                );
                $qb->setParameter('isActive', $isActiveBool);
            } catch (Throwable) {
                // pass
            }

            try {
                $carId = $request->query->get(self::FILTER_CAR_ID_KEY);

                assert(is_string($carId));

                $qb->andWhere(
                    $qb->expr()->eq(
                        RouteRepository::ALIAS . '.car',
                        ':carId'
                    )
                );
                $qb->setParameter('carId', $carId);
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::DESC);
        $routeGridSortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            RouteGridSortEnum::class,
            RouteGridSortEnum::DATE
        );
        $routesQueryBuilder = $this->routeRepository->getQueryBuilderWithAlias();
        $routesPaginator = $this->apiGridManager->createPaginator(
            $routesQueryBuilder,
            $queryModifier
        );
        /** @var RouteEntity[] $routes */
        $routes = $this->apiGridManager->fetchData(
            $routesQueryBuilder,
            $routeGridSortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );
        $routeListResponseDTO = $this->dtoFactory->createRouteListResponseDTO(
            $routes,
            $this->apiGridPropertyHelper->createPageCount(
                $routesPaginator->count(),
                $maxResults
            )
        );

        return $this->json($routeListResponseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'OilService',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: RouteDeleteResponseDTO::class
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
        '/oil-service/routes/{routeId}',
        name: 'oil_service_route_delete',
        methods: ['DELETE']
    )]
    public function delete(string $routeId): JsonResponse
    {
        $this->requireAdminUser();

        $route = $this->routeRepository->find($routeId);

        if ($route === null) {
            throw new NotFoundHttpException('Route not found');
        }

        $this->routeService->deleteRoute($route);

        $routeDeleteResponseDTO = $this->dtoFactory->createRouteDeleteResponseDTO();

        return $this->json($routeDeleteResponseDTO);
    }

    private function requireAdminUser(): AuthUser
    {
        $user = $this->security->getUser();

        if (!$user instanceof AuthUser) {
            throw new ServerErrorHttpException();
        }

        if (!$user->getIsAdmin()) {
            throw new AccessDeniedHttpException();
        }

        return $user;
    }
}
