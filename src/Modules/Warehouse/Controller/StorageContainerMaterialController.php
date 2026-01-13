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
use App\Modules\Warehouse\DTO\StorageContainerMaterialCreateRequestDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialCreateResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialDeleteResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialInfoResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialListResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialMoveAllRequestDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialMoveResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialMoveSelectedRequestDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialUpdateRequestDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialUpdateResponseDTO;
use App\Modules\Warehouse\Factory\DTOFactory;
use App\Modules\Warehouse\Grid\Enum\StorageContainerMaterialGridSortEnum;
use App\Warehouse\DBAL\Repository\StorageContainerMaterialRepository;
use App\Warehouse\DBAL\Entity\StorageContainerMaterial;
use App\Warehouse\StorageContainerMaterialService;
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

class StorageContainerMaterialController extends AbstractController
{
    private const string FILTER_STORAGE_CONTAINER_ID = 'storageContainerId';
    private const string FILTER_WASTE_MATERIAL_ID = 'wasteMaterialId';
    private const string FILTER_WAREHOUSE_ID = 'warehouseId';
    private const string FILTER_ROUTE_ID = 'routeId';
    private const string FILTER_RECYCLING_ID = 'recyclingId';
    private const string FILTER_IS_RECYCLED = 'isRecycled';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly StorageContainerMaterialRepository $storageContainerMaterialRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly StorageContainerMaterialService $storageContainerMaterialService,
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
                    type: StorageContainerMaterialCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Storage Container Materials',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: StorageContainerMaterialCreateResponseDTO::class
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
        '/warehouse/storage-container-materials',
        name: 'storage_container_material_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $user = $this->requireAdminUser();

        try {
            $createDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                StorageContainerMaterialCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($createDTO);

            $storageContainerMaterial = $this->storageContainerMaterialService->createStorageContainerMaterial(
                $createDTO->getStorageContainerId(),
                $createDTO->getWasteMaterialId(),
                $createDTO->getVolume(),
                $createDTO->getIsRecycled(),
                $user,
                $createDTO->getWarehouseId(),
                $createDTO->getRouteId(),
                $createDTO->getRecyclingId(),
                $createDTO->getOrderId(),
            );

            $responseDTO = $this->dtoFactory->createStorageContainerMaterialCreateResponseDTO($storageContainerMaterial);

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

    #[OA\Post(
        security: [
            [
                'Bearer' => []
            ],
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: StorageContainerMaterialMoveAllRequestDTO::class
                ),
            )
        ),
        tags: [
            'Storage Container Materials',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Moved',
                content: new Model(
                    type: StorageContainerMaterialMoveResponseDTO::class
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
        '/warehouse/storage-container-materials/{storageContainerId}/move-all',
        name: 'storage_container_material_move_all',
        methods: ['POST']
    )]
    public function moveAll(Request $request, string $storageContainerId): JsonResponse
    {
        $user = $this->requireAdminUser();

        try {
            $moveDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                StorageContainerMaterialMoveAllRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($moveDTO);

            $materials = $this->storageContainerMaterialService->moveNonRecycledMaterials(
                $storageContainerId,
                $moveDTO->getTargetStorageContainerId(),
                $user,
            );

            $responseDTO = $this->dtoFactory->createStorageContainerMaterialMoveResponseDTO($materials);

            return $this->json($responseDTO);
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection($e->getErrorCollection());
        } catch (InvalidDataException) {
            throw new BadRequestHttpException();
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
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
                    type: StorageContainerMaterialMoveSelectedRequestDTO::class
                ),
            )
        ),
        tags: [
            'Storage Container Materials',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Moved',
                content: new Model(
                    type: StorageContainerMaterialMoveResponseDTO::class
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
        '/warehouse/storage-containers/{storageContainerId}/assign-materials',
        name: 'storage_container_material_move_selected',
        methods: ['POST']
    )]
    public function moveSelected(Request $request, string $storageContainerId): JsonResponse
    {
        $user = $this->requireAdminUser();

        try {
            $moveDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                StorageContainerMaterialMoveSelectedRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($moveDTO);

            $materials = $this->storageContainerMaterialService->moveSelectedMaterials(
                $storageContainerId,
                $moveDTO->getStorageContainerMaterialIds(),
                $user,
            );

            $responseDTO = $this->dtoFactory->createStorageContainerMaterialMoveResponseDTO($materials);

            return $this->json($responseDTO);
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection($e->getErrorCollection());
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
                    type: StorageContainerMaterialUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Storage Container Materials',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: StorageContainerMaterialUpdateResponseDTO::class
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
        '/warehouse/storage-container-materials/{storageContainerMaterialId}',
        name: 'storage_container_material_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $storageContainerMaterialId): JsonResponse
    {
        $user = $this->requireAdminUser();

        $storageContainerMaterial = $this->storageContainerMaterialRepository->find($storageContainerMaterialId);

        if ($storageContainerMaterial === null) {
            throw new NotFoundHttpException('Storage container material not found');
        }

        try {
            $updateDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                StorageContainerMaterialUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($updateDTO);

            $storageContainerMaterial = $this->storageContainerMaterialService->updateStorageContainerMaterial(
                $storageContainerMaterial,
                $updateDTO->getStorageContainerId(),
                $updateDTO->getWasteMaterialId(),
                $updateDTO->getVolume(),
                $updateDTO->getIsRecycled(),
                $user,
                $updateDTO->getWarehouseId(),
                $updateDTO->getRouteId(),
                $updateDTO->getRecyclingId(),
                $updateDTO->getOrderId(),
            );

            $responseDTO = $this->dtoFactory->createStorageContainerMaterialUpdateResponseDTO($storageContainerMaterial);

            return $this->json($responseDTO);
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
            'Storage Container Materials',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: StorageContainerMaterialInfoResponseDTO::class
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
        '/warehouse/storage-container-materials/{storageContainerMaterialId}',
        name: 'storage_container_material_info',
        methods: ['GET']
    )]
    public function info(string $storageContainerMaterialId): JsonResponse
    {
        $this->requireAdminUser();

        $storageContainerMaterial = $this->storageContainerMaterialRepository->find($storageContainerMaterialId);

        if ($storageContainerMaterial === null) {
            throw new NotFoundHttpException('Storage container material not found');
        }

        $responseDTO = $this->dtoFactory->createStorageContainerMaterialInfoResponseDTO(
            $storageContainerMaterial,
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
            'Storage Container Materials',
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
                name: self::FILTER_WASTE_MATERIAL_ID,
                description: 'Filter by waste material ID',
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
                name: self::FILTER_RECYCLING_ID,
                description: 'Filter by recycling ID',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
            ),
            new OA\Parameter(
                name: self::FILTER_IS_RECYCLED,
                description: 'Filter by recycled flag',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean'),
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
                description: 'Sorting by values, default value createdAt',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'createdAt'
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
                    type: StorageContainerMaterialListResponseDTO::class
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
        '/warehouse/storage-container-materials',
        name: 'storage_container_material_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            try {
                $storageContainerId = $request->query->get(self::FILTER_STORAGE_CONTAINER_ID);

                assert(is_string($storageContainerId));

                $qb->andWhere(
                    $qb->expr()->eq(
                        StorageContainerMaterialRepository::ALIAS . '.storageContainer',
                        ':storageContainer'
                    )
                );
                $qb->setParameter('storageContainer', $storageContainerId);
            } catch (Throwable) {
                // pass
            }

            try {
                $wasteMaterialId = $request->query->get(self::FILTER_WASTE_MATERIAL_ID);

                assert(is_string($wasteMaterialId));

                $qb->andWhere(
                    $qb->expr()->eq(
                        StorageContainerMaterialRepository::ALIAS . '.wasteMaterial',
                        ':wasteMaterial'
                    )
                );
                $qb->setParameter('wasteMaterial', $wasteMaterialId);
            } catch (Throwable) {
                // pass
            }

            try {
                $warehouseId = $request->query->get(self::FILTER_WAREHOUSE_ID);

                assert(is_string($warehouseId));

                $qb->andWhere(
                    $qb->expr()->eq(
                        StorageContainerMaterialRepository::ALIAS . '.warehouse',
                        ':warehouse'
                    )
                );
                $qb->setParameter('warehouse', $warehouseId);
            } catch (Throwable) {
                // pass
            }

            try {
                $routeId = $request->query->get(self::FILTER_ROUTE_ID);

                assert(is_string($routeId));

                $qb->andWhere(
                    $qb->expr()->eq(
                        StorageContainerMaterialRepository::ALIAS . '.route',
                        ':route'
                    )
                );
                $qb->setParameter('route', $routeId);
            } catch (Throwable) {
                // pass
            }

            try {
                $recyclingId = $request->query->get(self::FILTER_RECYCLING_ID);

                assert(is_string($recyclingId));

                $qb->andWhere(
                    $qb->expr()->eq(
                        StorageContainerMaterialRepository::ALIAS . '.recycling',
                        ':recycling'
                    )
                );
                $qb->setParameter('recycling', $recyclingId);
            } catch (Throwable) {
                // pass
            }

            try {
                $isRecycled = $request->query->get(self::FILTER_IS_RECYCLED);

                if ($isRecycled === 'true' || $isRecycled === '1') {
                    $isRecycledBool = true;
                } elseif ($isRecycled === 'false' || $isRecycled === '0') {
                    $isRecycledBool = false;
                } else {
                    throw new InvalidDataException();
                }

                $qb->andWhere(
                    $qb->expr()->eq(
                        StorageContainerMaterialRepository::ALIAS . '.isRecycled',
                        ':isRecycled'
                    )
                );
                $qb->setParameter('isRecycled', $isRecycledBool);
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::DESC);
        $sortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            StorageContainerMaterialGridSortEnum::class,
            StorageContainerMaterialGridSortEnum::CREATED_AT
        );

        $queryBuilder = $this->storageContainerMaterialRepository->getQueryBuilderWithAlias();
        $paginator = $this->apiGridManager->createPaginator(
            $queryBuilder,
            $queryModifier
        );

        /** @var StorageContainerMaterial[] $storageContainerMaterials */
        $storageContainerMaterials = $this->apiGridManager->fetchData(
            $queryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier,
        );

        $responseDTO = $this->dtoFactory->createStorageContainerMaterialListResponseDTO(
            $storageContainerMaterials,
            $this->apiGridPropertyHelper->createPageCount(
                $paginator->count(),
                $maxResults
            )
        );

        return $this->json($responseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Storage Container Materials',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: StorageContainerMaterialDeleteResponseDTO::class
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
        '/warehouse/storage-container-materials/{storageContainerMaterialId}',
        name: 'storage_container_material_delete',
        methods: ['DELETE']
    )]
    public function delete(string $storageContainerMaterialId): JsonResponse
    {
        $this->requireAdminUser();

        $storageContainerMaterial = $this->storageContainerMaterialRepository->find($storageContainerMaterialId);

        if ($storageContainerMaterial === null) {
            throw new NotFoundHttpException('Storage container material not found');
        }

        try {
            $this->storageContainerMaterialService->deleteStorageContainerMaterial($storageContainerMaterial);
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection($e->getErrorCollection());
        }

        $responseDTO = $this->dtoFactory->createStorageContainerMaterialDeleteResponseDTO();

        return $this->json($responseDTO);
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
