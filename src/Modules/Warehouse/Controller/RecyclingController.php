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
use App\Modules\Warehouse\DTO\RecyclingCreateRequestDTO;
use App\Modules\Warehouse\DTO\RecyclingCreateResponseDTO;
use App\Modules\Warehouse\DTO\RecyclingDeleteResponseDTO;
use App\Modules\Warehouse\DTO\RecyclingInfoResponseDTO;
use App\Modules\Warehouse\DTO\RecyclingListResponseDTO;
use App\Modules\Warehouse\DTO\RecyclingRecycleResponseDTO;
use App\Modules\Warehouse\DTO\RecyclingUpdateRequestDTO;
use App\Modules\Warehouse\DTO\RecyclingUpdateResponseDTO;
use App\Modules\Warehouse\Factory\DTOFactory;
use App\Modules\Warehouse\Grid\Enum\RecyclingGridSortEnum;
use App\Warehouse\DBAL\Repository\RecyclingRepository;
use App\Warehouse\DBAL\Repository\StorageContainerMaterialRepository;
use App\Warehouse\DBAL\Entity\Recycling;
use App\Warehouse\DBAL\Entity\StorageContainerMaterial;
use App\Warehouse\RecyclingService;
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

class RecyclingController extends AbstractController
{
    private const string FILTER_RECYCLED_AT = 'recycledAt';
    private const string FILTER_STORAGE_CONTAINER_ID = 'storageContainerId';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly RecyclingRepository $recyclingRepository,
        private readonly StorageContainerMaterialRepository $storageContainerMaterialRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly RecyclingService $recyclingService,
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
                    type: RecyclingCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Recycling',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: RecyclingCreateResponseDTO::class
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
        '/warehouse/recyclings',
        name: 'recycling_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $user = $this->requireAdminUser();

        try {
            $createDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                RecyclingCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($createDTO);

            $recycledAt = $createDTO->getRecycledAt() !== null ? new DateTimeImmutable($createDTO->getRecycledAt()) : null;

            $recycling = $this->recyclingService->createRecycling(
                $recycledAt,
                null,
                $user,
                $createDTO->getStorageContainerIds(),
            );

            $materials = $this->storageContainerMaterialRepository->findBy([
                'recycling' => $recycling,
            ]);

            $responseDTO = $this->dtoFactory->createRecyclingCreateResponseDTO($recycling, $materials);

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
                    type: RecyclingUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Recycling',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: RecyclingUpdateResponseDTO::class
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
        '/warehouse/recyclings/{recyclingId}',
        name: 'recycling_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $recyclingId): JsonResponse
    {
        $user = $this->requireAdminUser();

        $recycling = $this->recyclingRepository->find($recyclingId);

        if ($recycling === null) {
            throw new NotFoundHttpException('Recycling not found');
        }

        try {
            $updateDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                RecyclingUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($updateDTO);

            $recycledAt = $updateDTO->getRecycledAt() !== null ? new DateTimeImmutable($updateDTO->getRecycledAt()) : null;

            $recycling = $this->recyclingService->updateRecycling(
                $recycling,
                $recycledAt,
                null,
                $user,
                $updateDTO->getStorageContainerIds(),
            );

            $materials = $this->storageContainerMaterialRepository->findBy([
                'recycling' => $recycling,
            ]);

            $responseDTO = $this->dtoFactory->createRecyclingUpdateResponseDTO($recycling, $materials);

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
            'Recycling',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: RecyclingInfoResponseDTO::class
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
        '/warehouse/recyclings/{recyclingId}',
        name: 'recycling_info',
        methods: ['GET']
    )]
    public function info(string $recyclingId): JsonResponse
    {
        $this->requireAdminUser();

        $recycling = $this->recyclingRepository->find($recyclingId);

        if ($recycling === null) {
            throw new NotFoundHttpException('Recycling not found');
        }

        $materials = $this->storageContainerMaterialRepository->findBy([
            'recycling' => $recycling,
        ]);

        $responseDTO = $this->dtoFactory->createRecyclingInfoResponseDTO(
            $recycling,
            $materials,
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
            'Recycling',
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
                name: self::FILTER_RECYCLED_AT,
                description: 'Filter by recycled date (YYYY-MM-DD)',
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
                    type: RecyclingListResponseDTO::class
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
        '/warehouse/recyclings',
        name: 'recycling_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            try {
                $recycledAt = $request->query->get(self::FILTER_RECYCLED_AT);

                assert(is_string($recycledAt));

                $qb->andWhere(
                    $qb->expr()->eq(
                        RecyclingRepository::ALIAS . '.recycledAt',
                        ':recycledAt'
                    )
                );
                $qb->setParameter('recycledAt', new DateTimeImmutable($recycledAt));
            } catch (Throwable) {
                // pass
            }

            try {
                $storageContainerId = $request->query->get(self::FILTER_STORAGE_CONTAINER_ID);

                assert(is_string($storageContainerId));

                $qb->leftJoin(RecyclingRepository::ALIAS . '.storageContainers', 'rrsc');
                $qb->andWhere(
                    $qb->expr()->eq(
                        'rrsc.id',
                        ':storageContainer'
                    )
                );
                $qb->setParameter('storageContainer', $storageContainerId);
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::DESC);
        $sortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            RecyclingGridSortEnum::class,
            RecyclingGridSortEnum::CREATED_AT
        );

        $queryBuilder = $this->recyclingRepository->getQueryBuilderWithAlias();
        $paginator = $this->apiGridManager->createPaginator(
            $queryBuilder,
            $queryModifier,
        );

        /** @var Recycling[] $recyclings */
        $recyclings = $this->apiGridManager->fetchData(
            $queryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier,
        );

        $materials = [];

        if ($recyclings !== []) {
            /** @var StorageContainerMaterial[] $materialsResult */
            $materialsResult = $this->storageContainerMaterialRepository->findBy([
                'recycling' => $recyclings,
            ]);

            foreach ($materialsResult as $material) {
                $recyclingId = $material->getRecycling()?->getId()->__toString();

                if ($recyclingId === null) {
                    continue;
                }

                $materials[$recyclingId][] = $material;
            }
        }

        $responseDTO = $this->dtoFactory->createRecyclingListResponseDTO(
            $recyclings,
            $this->apiGridPropertyHelper->createPageCount(
                $paginator->count(),
                $maxResults
            ),
            $materials,
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
            'Recycling',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: RecyclingDeleteResponseDTO::class
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
        '/warehouse/recyclings/{recyclingId}',
        name: 'recycling_delete',
        methods: ['DELETE']
    )]
    public function delete(string $recyclingId): JsonResponse
    {
        $this->requireAdminUser();

        $recycling = $this->recyclingRepository->find($recyclingId);

        if ($recycling === null) {
            throw new NotFoundHttpException('Recycling not found');
        }

        $this->recyclingService->deleteRecycling($recycling);

        $responseDTO = $this->dtoFactory->createRecyclingDeleteResponseDTO();

        return $this->json($responseDTO);
    }

    #[OA\Post(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Recycling',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Recycled',
                content: new Model(
                    type: RecyclingRecycleResponseDTO::class
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
        '/warehouse/recyclings/{recyclingId}/recycle',
        name: 'recycling_recycle',
        methods: ['POST']
    )]
    public function recycle(string $recyclingId): JsonResponse
    {
        $user = $this->requireAdminUser();

        $recycling = $this->recyclingRepository->find($recyclingId);

        if ($recycling === null) {
            throw new NotFoundHttpException('Recycling not found');
        }

        $this->recyclingService->recycle($recycling, $user);

        $materials = $this->storageContainerMaterialRepository->findBy([
            'recycling' => $recycling,
        ]);

        $responseDTO = $this->dtoFactory->createRecyclingRecycleResponseDTO(
            $recycling,
            $materials,
        );

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
