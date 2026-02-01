<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Core\Helper\QueryParameterParser;
use App\Domain\ApiGrid\ApiGridManager;
use App\Domain\ApiGrid\ApiGridPropertyHelper;
use App\Domain\ApiGrid\OrderEnum;
use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Modules\Warehouse\DTO\WarehouseCreateRequestDTO;
use App\Modules\Warehouse\DTO\WarehouseCreateResponseDTO;
use App\Modules\Warehouse\DTO\WarehouseDeleteResponseDTO;
use App\Modules\Warehouse\DTO\WarehouseInfoResponseDTO;
use App\Modules\Warehouse\DTO\WarehouseListResponseDTO;
use App\Modules\Warehouse\DTO\WarehouseUpdateRequestDTO;
use App\Modules\Warehouse\DTO\WarehouseUpdateResponseDTO;
use App\Modules\Warehouse\Factory\DTOFactory;
use App\Modules\Warehouse\Grid\Enum\WarehouseGridSortEnum;
use App\Warehouse\DBAL\Repository\StorageContainerLocationRepository;
use App\Warehouse\DBAL\Repository\WarehouseRepository;
use App\Warehouse\DBAL\Entity\Warehouse;
use App\Warehouse\WarehouseService;
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

class WarehouseController extends AbstractController
{
    private const string FILTER_LABEL_KEY = 'label';
    private const string FILTER_SHORT_LABEL_KEY = 'shortLabel';
    private const string FILTER_IS_ACTIVE_KEY = 'isActive';
    private const string FILTER_IS_GARAGE_KEY = 'isGarage';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly WarehouseRepository $warehouseRepository,
        private readonly StorageContainerLocationRepository $storageContainerLocationRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly WarehouseService $warehouseService,
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
                    type: WarehouseCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Warehouses',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: WarehouseCreateResponseDTO::class
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
        '/warehouse/warehouses',
        name: 'warehouse_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $warehouseCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                WarehouseCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($warehouseCreateRequestDTO);

            $warehouse = $this->warehouseService->createWarehouse(
                $warehouseCreateRequestDTO->getLabel(),
                $warehouseCreateRequestDTO->getShortLabel(),
                $warehouseCreateRequestDTO->getIsActive(),
                $warehouseCreateRequestDTO->getAddress(),
                $warehouseCreateRequestDTO->getLatitude(),
                $warehouseCreateRequestDTO->getLongitude(),
                $warehouseCreateRequestDTO->getIsGarage(),
            );

            $warehouseCreateResponseDTO = $this->dtoFactory->createWarehouseCreateResponseDTO($warehouse);

            return $this->json($warehouseCreateResponseDTO);
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
                    type: WarehouseUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Warehouses',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: WarehouseUpdateResponseDTO::class
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
        '/warehouse/warehouses/{warehouseId}',
        name: 'warehouse_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $warehouseId): JsonResponse
    {
        $this->requireAdminUser();

        $warehouse = $this->warehouseRepository->find($warehouseId);

        if ($warehouse === null) {
            throw new NotFoundHttpException('Warehouse not found');
        }

        try {
            $warehouseUpdateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                WarehouseUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($warehouseUpdateRequestDTO);

            $warehouse = $this->warehouseService->updateWarehouse(
                $warehouse,
                $warehouseUpdateRequestDTO->getLabel(),
                $warehouseUpdateRequestDTO->getShortLabel(),
                $warehouseUpdateRequestDTO->getIsActive(),
                $warehouseUpdateRequestDTO->getAddress(),
                $warehouseUpdateRequestDTO->getLatitude(),
                $warehouseUpdateRequestDTO->getLongitude(),
                $warehouseUpdateRequestDTO->getIsGarage(),
            );

            $warehouseUpdateResponseDTO = $this->dtoFactory->createWarehouseUpdateResponseDTO($warehouse);

            return $this->json($warehouseUpdateResponseDTO);
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
            'Warehouses',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: WarehouseInfoResponseDTO::class
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
        '/warehouse/warehouses/{warehouseId}',
        name: 'warehouse_info',
        methods: ['GET']
    )]
    public function info(string $warehouseId): JsonResponse
    {
        $this->requireAdminUser();

        $warehouse = $this->warehouseRepository->find($warehouseId);

        if ($warehouse === null) {
            throw new NotFoundHttpException('Warehouse not found');
        }

        $currentLocations = $this->storageContainerLocationRepository->findLatestForWarehouse($warehouse);

        $warehouseInfoResponseDTO = $this->dtoFactory->createWarehouseInfoResponseDTO(
            $warehouse,
            $currentLocations,
        );

        return $this->json($warehouseInfoResponseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Warehouses',
        ],
        parameters: [
            new OA\Parameter(
                name: self::FILTER_LABEL_KEY,
                description: 'Filter by label (non-strict)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'Central'
            ),
            new OA\Parameter(
                name: self::FILTER_SHORT_LABEL_KEY,
                description: 'Filter by short label (non-strict)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'CW'
            ),
            new OA\Parameter(
                name: self::FILTER_IS_ACTIVE_KEY,
                description: 'Filter by activity flag',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'boolean'
                ),
                example: true
            ),
            new OA\Parameter(
                name: self::FILTER_IS_GARAGE_KEY,
                description: 'Filter by garage flag',
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
                description: 'Sorting by values, default value label',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'label'
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::ORDER_KEY,
                description: 'Select ordering, default value ASC, values: ASC, DESC',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'ASC'
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: WarehouseListResponseDTO::class
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
        '/warehouse/warehouses',
        name: 'warehouse_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof AuthUser) {
            throw new AccessDeniedHttpException();
        }

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            try {
                $label = $request->query->get(self::FILTER_LABEL_KEY);

                assert(is_string($label));

                $qb->andWhere(
                    $qb->expr()->like(
                        WarehouseRepository::ALIAS . '.label',
                        ':label'
                    )
                );
                $qb->setParameter('label', '%' . $label . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $shortLabel = $request->query->get(self::FILTER_SHORT_LABEL_KEY);

                assert(is_string($shortLabel));

                $qb->andWhere(
                    $qb->expr()->like(
                        WarehouseRepository::ALIAS . '.shortLabel',
                        ':shortLabel'
                    )
                );
                $qb->setParameter('shortLabel', '%' . $shortLabel . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $isActive = $request->query->get(self::FILTER_IS_ACTIVE_KEY);

                if ($isActive !== null) {
                    $isActiveBool = QueryParameterParser::parseBoolean($isActive);

                    $qb->andWhere(
                        $qb->expr()->eq(
                            WarehouseRepository::ALIAS . '.isActive',
                            ':isActive'
                        )
                    );
                    $qb->setParameter('isActive', $isActiveBool);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $isGarage = $request->query->get(self::FILTER_IS_GARAGE_KEY);

                if ($isGarage !== null) {
                    $isGarageBool = QueryParameterParser::parseBoolean($isGarage);

                    $qb->andWhere(
                        $qb->expr()->eq(
                            WarehouseRepository::ALIAS . '.isGarage',
                            ':isGarage'
                        )
                    );
                    $qb->setParameter('isGarage', $isGarageBool);
                }
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::ASC);
        $sortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            WarehouseGridSortEnum::class,
            WarehouseGridSortEnum::LABEL
        );

        $warehousesQueryBuilder = $this->warehouseRepository->getQueryBuilderWithAlias();
        $warehousesPaginator = $this->apiGridManager->createPaginator(
            $warehousesQueryBuilder,
            $queryModifier
        );

        /** @var Warehouse[] $warehouses */
        $warehouses = $this->apiGridManager->fetchData(
            $warehousesQueryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier,
        );

        $warehouseListResponseDTO = $this->dtoFactory->createWarehouseListResponseDTO(
            $warehouses,
            $this->apiGridPropertyHelper->createPageCount(
                $warehousesPaginator->count(),
                $maxResults
            )
        );

        return $this->json($warehouseListResponseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Warehouses',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: WarehouseDeleteResponseDTO::class
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
        '/warehouse/warehouses/{warehouseId}',
        name: 'warehouse_delete',
        methods: ['DELETE']
    )]
    public function delete(string $warehouseId): JsonResponse
    {
        $this->requireAdminUser();

        $warehouse = $this->warehouseRepository->find($warehouseId);

        if ($warehouse === null) {
            throw new NotFoundHttpException('Warehouse not found');
        }

        $this->warehouseService->deleteWarehouse($warehouse);

        $warehouseDeleteResponseDTO = $this->dtoFactory->createWarehouseDeleteResponseDTO();

        return $this->json($warehouseDeleteResponseDTO);
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
