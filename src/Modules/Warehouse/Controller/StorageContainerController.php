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
use App\Modules\Warehouse\DTO\StorageContainerCreateRequestDTO;
use App\Modules\Warehouse\DTO\StorageContainerCreateResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerDeleteResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerInfoResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerListResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerUpdateRequestDTO;
use App\Modules\Warehouse\DTO\StorageContainerUpdateResponseDTO;
use App\Modules\Warehouse\Factory\DTOFactory;
use App\Modules\Warehouse\Grid\Enum\StorageContainerGridSortEnum;
use App\Modules\Warehouse\Validation\Constraint\UniqueStorageContainerCode;
use App\Warehouse\DBAL\Enum\StorageContainerTypeEnum;
use App\Warehouse\DBAL\Enum\VolumeUnitEnum;
use App\Warehouse\DBAL\Repository\StorageContainerLocationRepository;
use App\Warehouse\DBAL\Repository\StorageContainerMaterialRepository;
use App\Warehouse\DBAL\Repository\StorageContainerRepository;
use App\Warehouse\DBAL\Entity\StorageContainer;
use App\Warehouse\StorageContainerService;
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

class StorageContainerController extends AbstractController
{
    private const string FILTER_CODE_KEY = 'code';
    private const string FILTER_DESCRIPTION_KEY = 'description';
    private const string FILTER_IS_ACTIVE_KEY = 'isActive';
    private const string FILTER_TYPE_KEY = 'type';
    private const string FILTER_VOLUME_UNIT_KEY = 'volumeUnit';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly StorageContainerRepository $storageContainerRepository,
        private readonly StorageContainerLocationRepository $storageContainerLocationRepository,
        private readonly StorageContainerMaterialRepository $storageContainerMaterialRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly StorageContainerService $storageContainerService,
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
                    type: StorageContainerCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Storage Containers',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: StorageContainerCreateResponseDTO::class
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
        '/warehouse/storage-containers',
        name: 'storage_container_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $storageContainerCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                StorageContainerCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($storageContainerCreateRequestDTO);

            $storageContainer = $this->storageContainerService->createStorageContainer(
                $storageContainerCreateRequestDTO->getCode(),
                StorageContainerTypeEnum::from($storageContainerCreateRequestDTO->getType()),
                $storageContainerCreateRequestDTO->getCapacity(),
                VolumeUnitEnum::from($storageContainerCreateRequestDTO->getVolumeUnit()),
                $storageContainerCreateRequestDTO->getIsActive(),
                $storageContainerCreateRequestDTO->getDescription(),
                $storageContainerCreateRequestDTO->getPreferredWasteMaterialIds(),
            );

            $actualLocations = $this->storageContainerLocationRepository->findLatestByStorageContainerIds([
                $storageContainer->getId()->__toString(),
            ]);

            $currentMaterialsMap = $this->storageContainerMaterialRepository->findCurrentByStorageContainerIds([
                $storageContainer->getId()->__toString(),
            ]);
            $currentMaterials = $currentMaterialsMap[$storageContainer->getId()->__toString()] ?? [];

            $storageContainerCreateResponseDTO = $this->dtoFactory->createStorageContainerCreateResponseDTO(
                $storageContainer,
                $actualLocations[$storageContainer->getId()->__toString()] ?? null,
                $currentMaterials,
            );

            return $this->json($storageContainerCreateResponseDTO);
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
                    type: StorageContainerUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Storage Containers',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: StorageContainerUpdateResponseDTO::class
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
        '/warehouse/storage-containers/{storageContainerId}',
        name: 'storage_container_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $storageContainerId): JsonResponse
    {
        $this->requireAdminUser();

        $storageContainer = $this->storageContainerRepository->find($storageContainerId);

        if ($storageContainer === null) {
            throw new NotFoundHttpException('Storage container not found');
        }

        try {
            $storageContainerUpdateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                StorageContainerUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO(
                $storageContainerUpdateRequestDTO,
                new UniqueStorageContainerCode(ignoreStorageContainerId: $storageContainer->getId()->__toString()),
            );

            $storageContainer = $this->storageContainerService->updateStorageContainer(
                $storageContainer,
                $storageContainerUpdateRequestDTO->getCode(),
                StorageContainerTypeEnum::from($storageContainerUpdateRequestDTO->getType()),
                $storageContainerUpdateRequestDTO->getCapacity(),
                VolumeUnitEnum::from($storageContainerUpdateRequestDTO->getVolumeUnit()),
                $storageContainerUpdateRequestDTO->getIsActive(),
                $storageContainerUpdateRequestDTO->getDescription(),
                $storageContainerUpdateRequestDTO->getPreferredWasteMaterialIds(),
            );

            $actualLocations = $this->storageContainerLocationRepository->findLatestByStorageContainerIds([
                $storageContainer->getId()->__toString(),
            ]);

            $currentMaterialsMap = $this->storageContainerMaterialRepository->findCurrentByStorageContainerIds([
                $storageContainer->getId()->__toString(),
            ]);
            $currentMaterials = $currentMaterialsMap[$storageContainer->getId()->__toString()] ?? [];

            $storageContainerUpdateResponseDTO = $this->dtoFactory->createStorageContainerUpdateResponseDTO(
                $storageContainer,
                $actualLocations[$storageContainer->getId()->__toString()] ?? null,
                $currentMaterials,
            );

            return $this->json($storageContainerUpdateResponseDTO);
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
            'Storage Containers',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: StorageContainerInfoResponseDTO::class
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
        '/warehouse/storage-containers/{storageContainerId}',
        name: 'storage_container_info',
        methods: ['GET']
    )]
    public function info(string $storageContainerId): JsonResponse
    {
        $this->requireAdminUser();

        $storageContainer = $this->storageContainerRepository->find($storageContainerId);

        if ($storageContainer === null) {
            throw new NotFoundHttpException('Storage container not found');
        }

        $actualLocations = $this->storageContainerLocationRepository->findLatestByStorageContainerIds([
            $storageContainer->getId()->__toString(),
        ]);

        $currentMaterialsMap = $this->storageContainerMaterialRepository->findCurrentByStorageContainerIds([
            $storageContainer->getId()->__toString(),
        ]);
        $currentMaterials = $currentMaterialsMap[$storageContainer->getId()->__toString()] ?? [];

        $storageContainerInfoResponseDTO = $this->dtoFactory->createStorageContainerInfoResponseDTO(
            $storageContainer,
            $actualLocations[$storageContainer->getId()->__toString()] ?? null,
            $currentMaterials,
        );

        return $this->json($storageContainerInfoResponseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Storage Containers',
        ],
        parameters: [
            new OA\Parameter(
                name: self::FILTER_CODE_KEY,
                description: 'Filter by code (non-strict)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'SC-001'
            ),
            new OA\Parameter(
                name: self::FILTER_DESCRIPTION_KEY,
                description: 'Filter by description (non-strict)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'oil'
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
                name: self::FILTER_TYPE_KEY,
                description: 'Filter by type',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: StorageContainerTypeEnum::VALUES
                ),
                example: 'barrel'
            ),
            new OA\Parameter(
                name: self::FILTER_VOLUME_UNIT_KEY,
                description: 'Filter by volume unit',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: VolumeUnitEnum::VALUES
                ),
                example: 'l'
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
                description: 'Sorting by values, default value code',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'code'
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
                    type: StorageContainerListResponseDTO::class
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
        '/warehouse/storage-containers',
        name: 'storage_container_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            try {
                $code = $request->query->get(self::FILTER_CODE_KEY);

                assert(is_string($code));

                $qb->andWhere(
                    $qb->expr()->like(
                        StorageContainerRepository::ALIAS . '.code',
                        ':code'
                    )
                );
                $qb->setParameter('code', '%' . $code . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $description = $request->query->get(self::FILTER_DESCRIPTION_KEY);

                assert(is_string($description));

                $qb->andWhere(
                    $qb->expr()->like(
                        StorageContainerRepository::ALIAS . '.description',
                        ':description'
                    )
                );
                $qb->setParameter('description', '%' . $description . '%');
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
                        StorageContainerRepository::ALIAS . '.isActive',
                        ':isActive'
                    )
                );
                $qb->setParameter('isActive', $isActiveBool);
            } catch (Throwable) {
                // pass
            }

            try {
                $type = $request->query->get(self::FILTER_TYPE_KEY);

                assert(is_string($type));

                $typeEnum = StorageContainerTypeEnum::tryFrom($type);

                if ($typeEnum !== null) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            StorageContainerRepository::ALIAS . '.type',
                            ':type'
                        )
                    );
                    $qb->setParameter('type', $typeEnum->value);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $volumeUnit = $request->query->get(self::FILTER_VOLUME_UNIT_KEY);

                assert(is_string($volumeUnit));

                $volumeUnitEnum = VolumeUnitEnum::tryFrom($volumeUnit);

                if ($volumeUnitEnum !== null) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            StorageContainerRepository::ALIAS . '.volumeUnit',
                            ':volumeUnit'
                        )
                    );
                    $qb->setParameter('volumeUnit', $volumeUnitEnum->value);
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
            StorageContainerGridSortEnum::class,
            StorageContainerGridSortEnum::CODE
        );

        $storageContainersQueryBuilder = $this->storageContainerRepository->getQueryBuilderWithAlias();
        $storageContainersPaginator = $this->apiGridManager->createPaginator(
            $storageContainersQueryBuilder,
            $queryModifier
        );

        /** @var StorageContainer[] $storageContainers */
        $storageContainers = $this->apiGridManager->fetchData(
            $storageContainersQueryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier,
        );

        $storageContainerIds = array_map(
            static fn ($storageContainer) => $storageContainer->getId()->__toString(),
            $storageContainers
        );

        $actualLocations = $this->storageContainerLocationRepository->findLatestByStorageContainerIds($storageContainerIds);
        $currentMaterials = $this->storageContainerMaterialRepository->findCurrentByStorageContainerIds($storageContainerIds);

        $storageContainerListResponseDTO = $this->dtoFactory->createStorageContainerListResponseDTO(
            $storageContainers,
            $actualLocations,
            $this->apiGridPropertyHelper->createPageCount(
                $storageContainersPaginator->count(),
                $maxResults
            ),
            $currentMaterials,
        );

        return $this->json($storageContainerListResponseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Storage Containers',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: StorageContainerDeleteResponseDTO::class
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
        '/warehouse/storage-containers/{storageContainerId}',
        name: 'storage_container_delete',
        methods: ['DELETE']
    )]
    public function delete(string $storageContainerId): JsonResponse
    {
        $this->requireAdminUser();

        $storageContainer = $this->storageContainerRepository->find($storageContainerId);

        if ($storageContainer === null) {
            throw new NotFoundHttpException('Storage container not found');
        }

        try {
            $this->storageContainerService->deleteStorageContainer($storageContainer);
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection($e->getErrorCollection());
        }

        $storageContainerDeleteResponseDTO = $this->dtoFactory->createStorageContainerDeleteResponseDTO();

        return $this->json($storageContainerDeleteResponseDTO);
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
