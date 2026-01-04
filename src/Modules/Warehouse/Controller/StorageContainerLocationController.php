<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Controller;

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
use App\Modules\Warehouse\DTO\StorageContainerLocationCreateRequestDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationCreateResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationDeleteResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationInfoResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationListResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationUpdateRequestDTO;
use App\Modules\Warehouse\DTO\StorageContainerLocationUpdateResponseDTO;
use App\Modules\Warehouse\Factory\DTOFactory;
use App\Modules\Warehouse\Grid\Enum\StorageContainerLocationGridSortEnum;
use App\OilService\DBAL\Repository\RouteRepository;
use App\Warehouse\DBAL\Repository\StorageContainerLocationRepository;
use App\Warehouse\DBAL\Repository\StorageContainerRepository;
use App\Warehouse\DBAL\Repository\WarehouseRepository;
use App\Warehouse\StorageContainerLocationService;
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

class StorageContainerLocationController extends AbstractController
{
    private const string FILTER_STORAGE_CONTAINER_ID = 'storageContainerId';
    private const string FILTER_WAREHOUSE_ID = 'warehouseId';
    private const string FILTER_ROUTE_ID = 'routeId';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly StorageContainerLocationRepository $storageContainerLocationRepository,
        private readonly StorageContainerRepository $storageContainerRepository,
        private readonly WarehouseRepository $warehouseRepository,
        private readonly RouteRepository $routeRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly StorageContainerLocationService $storageContainerLocationService,
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
                    type: StorageContainerLocationCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Warehouse',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: StorageContainerLocationCreateResponseDTO::class
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
        '/warehouse/storage-container-locations',
        name: 'storage_container_location_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $storageContainerLocationCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                StorageContainerLocationCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($storageContainerLocationCreateRequestDTO);

            $storageContainerLocation = $this->storageContainerLocationService->createStorageContainerLocation(
                $storageContainerLocationCreateRequestDTO->getStorageContainerId(),
                $storageContainerLocationCreateRequestDTO->getWarehouseId(),
                $storageContainerLocationCreateRequestDTO->getRouteId(),
                new DateTimeImmutable($storageContainerLocationCreateRequestDTO->getMovedAt()),
            );

            $storageContainerLocationCreateResponseDTO = $this->dtoFactory->createStorageContainerLocationCreateResponseDTO(
                $storageContainerLocation
            );

            return $this->json($storageContainerLocationCreateResponseDTO);
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
                    type: StorageContainerLocationUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Warehouse',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: StorageContainerLocationUpdateResponseDTO::class
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
        '/warehouse/storage-container-locations/{storageContainerLocationId}',
        name: 'storage_container_location_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $storageContainerLocationId): JsonResponse
    {
        $this->requireAdminUser();

        $storageContainerLocation = $this->storageContainerLocationRepository->find($storageContainerLocationId);

        if ($storageContainerLocation === null) {
            throw new NotFoundHttpException('Storage container location not found');
        }

        try {
            $storageContainerLocationUpdateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                StorageContainerLocationUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($storageContainerLocationUpdateRequestDTO);

            $storageContainerLocation = $this->storageContainerLocationService->updateStorageContainerLocation(
                $storageContainerLocation,
                $storageContainerLocationUpdateRequestDTO->getStorageContainerId(),
                $storageContainerLocationUpdateRequestDTO->getWarehouseId(),
                $storageContainerLocationUpdateRequestDTO->getRouteId(),
                new DateTimeImmutable($storageContainerLocationUpdateRequestDTO->getMovedAt()),
            );

            $storageContainerLocationUpdateResponseDTO = $this->dtoFactory->createStorageContainerLocationUpdateResponseDTO(
                $storageContainerLocation
            );

            return $this->json($storageContainerLocationUpdateResponseDTO);
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection($e->getErrorCollection());
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
            'Warehouse',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: StorageContainerLocationInfoResponseDTO::class
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
        '/warehouse/storage-container-locations/{storageContainerLocationId}',
        name: 'storage_container_location_info',
        methods: ['GET']
    )]
    public function info(string $storageContainerLocationId): JsonResponse
    {
        $this->requireAdminUser();

        $storageContainerLocation = $this->storageContainerLocationRepository->find($storageContainerLocationId);

        if ($storageContainerLocation === null) {
            throw new NotFoundHttpException('Storage container location not found');
        }

        $storageContainerLocationInfoResponseDTO = $this->dtoFactory->createStorageContainerLocationInfoResponseDTO(
            $storageContainerLocation
        );

        return $this->json($storageContainerLocationInfoResponseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Warehouse',
        ],
        parameters: [
            new OA\Parameter(
                name: self::FILTER_STORAGE_CONTAINER_ID,
                description: 'Filter by storage container ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
            ),
            new OA\Parameter(
                name: self::FILTER_WAREHOUSE_ID,
                description: 'Filter by warehouse ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
            ),
            new OA\Parameter(
                name: self::FILTER_ROUTE_ID,
                description: 'Filter by route ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
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
                description: 'Sorting by values, default value movedAt',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'movedAt'
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
                    type: StorageContainerLocationListResponseDTO::class
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
        '/warehouse/storage-container-locations',
        name: 'storage_container_location_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            $qb->leftJoin(StorageContainerLocationRepository::ALIAS . '.storageContainer', 'sc');
            $qb->addSelect('sc');
            $qb->leftJoin(StorageContainerLocationRepository::ALIAS . '.warehouse', 'w');
            $qb->addSelect('w');
            $qb->leftJoin(StorageContainerLocationRepository::ALIAS . '.route', 'r');
            $qb->addSelect('r');

            try {
                $storageContainerId = $request->query->get(self::FILTER_STORAGE_CONTAINER_ID);

                assert(is_string($storageContainerId));

                $qb->andWhere(
                    $qb->expr()->eq(StorageContainerLocationRepository::ALIAS . '.storageContainer', ':storageContainerId')
                );
                $qb->setParameter('storageContainerId', $storageContainerId);
            } catch (Throwable) {
                // pass
            }

            try {
                $warehouseId = $request->query->get(self::FILTER_WAREHOUSE_ID);

                assert(is_string($warehouseId));

                $qb->andWhere(
                    $qb->expr()->eq(StorageContainerLocationRepository::ALIAS . '.warehouse', ':warehouseId')
                );
                $qb->setParameter('warehouseId', $warehouseId);
            } catch (Throwable) {
                // pass
            }

            try {
                $routeId = $request->query->get(self::FILTER_ROUTE_ID);

                assert(is_string($routeId));

                $qb->andWhere(
                    $qb->expr()->eq(StorageContainerLocationRepository::ALIAS . '.route', ':routeId')
                );
                $qb->setParameter('routeId', $routeId);
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::DESC);
        $sortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            StorageContainerLocationGridSortEnum::class,
            StorageContainerLocationGridSortEnum::MOVED_AT
        );

        $storageContainerLocationsQueryBuilder = $this->storageContainerLocationRepository->getQueryBuilderWithAlias();
        $storageContainerLocationsPaginator = $this->apiGridManager->createPaginator(
            $storageContainerLocationsQueryBuilder,
            $queryModifier
        );

        $storageContainerLocations = $this->apiGridManager->fetchData(
            $storageContainerLocationsQueryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier,
        );

        $storageContainerLocationListResponseDTO = $this->dtoFactory->createStorageContainerLocationListResponseDTO(
            $storageContainerLocations,
            $this->apiGridPropertyHelper->createPageCount(
                $storageContainerLocationsPaginator->count(),
                $maxResults
            )
        );

        return $this->json($storageContainerLocationListResponseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Warehouse',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: StorageContainerLocationDeleteResponseDTO::class
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
        '/warehouse/storage-container-locations/{storageContainerLocationId}',
        name: 'storage_container_location_delete',
        methods: ['DELETE']
    )]
    public function delete(string $storageContainerLocationId): JsonResponse
    {
        $this->requireAdminUser();

        $storageContainerLocation = $this->storageContainerLocationRepository->find($storageContainerLocationId);

        if ($storageContainerLocation === null) {
            throw new NotFoundHttpException('Storage container location not found');
        }

        try {
            $this->storageContainerLocationService->deleteStorageContainerLocation($storageContainerLocation);
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }

        $storageContainerLocationDeleteResponseDTO = $this->dtoFactory->createStorageContainerLocationDeleteResponseDTO();

        return $this->json($storageContainerLocationDeleteResponseDTO);
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
