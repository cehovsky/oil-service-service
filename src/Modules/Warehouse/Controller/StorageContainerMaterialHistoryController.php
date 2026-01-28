<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Domain\ApiGrid\ApiGridManager;
use App\Domain\ApiGrid\ApiGridPropertyHelper;
use App\Domain\ApiGrid\OrderEnum;
use App\Domain\Exception\ServerErrorHttpException;
use App\Modules\Warehouse\DTO\StorageContainerMaterialHistoryDeleteResponseDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialHistoryListResponseDTO;
use App\Modules\Warehouse\Factory\DTOFactory;
use App\Modules\Warehouse\Grid\Enum\StorageContainerMaterialHistoryGridSortEnum;
use App\Warehouse\DBAL\Repository\StorageContainerMaterialHistoryRepository;
use App\Warehouse\DBAL\Entity\StorageContainerMaterialHistory;
use Doctrine\ORM\EntityManagerInterface;
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

class StorageContainerMaterialHistoryController extends AbstractController
{
    private const string FILTER_STORAGE_CONTAINER_ID = 'storageContainerId';
    private const string FILTER_STORAGE_CONTAINER_MATERIAL_ID = 'storageContainerMaterialId';

    public function __construct(
        private readonly DTOFactory $dtoFactory,
        private readonly StorageContainerMaterialHistoryRepository $storageContainerMaterialHistoryRepository,
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
            'Storage Container Material History',
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
                name: self::FILTER_STORAGE_CONTAINER_MATERIAL_ID,
                description: 'Filter by storage container material ID',
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
                    type: StorageContainerMaterialHistoryListResponseDTO::class
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
        '/warehouse/storage-container-material-history',
        name: 'storage_container_material_history_list',
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
                        StorageContainerMaterialHistoryRepository::ALIAS . '.storageContainer',
                        ':storageContainer'
                    )
                );
                $qb->setParameter('storageContainer', $storageContainerId);
            } catch (Throwable) {
                // pass
            }

            try {
                $storageContainerMaterialId = $request->query->get(self::FILTER_STORAGE_CONTAINER_MATERIAL_ID);

                assert(is_string($storageContainerMaterialId));

                $qb->andWhere(
                    $qb->expr()->eq(
                        StorageContainerMaterialHistoryRepository::ALIAS . '.storageContainerMaterial',
                        ':storageContainerMaterial'
                    )
                );
                $qb->setParameter('storageContainerMaterial', $storageContainerMaterialId);
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::DESC);
        $sortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            StorageContainerMaterialHistoryGridSortEnum::class,
            StorageContainerMaterialHistoryGridSortEnum::CREATED_AT
        );

        $queryBuilder = $this->storageContainerMaterialHistoryRepository->getQueryBuilderWithAlias();
        $paginator = $this->apiGridManager->createPaginator(
            $queryBuilder,
            $queryModifier,
        );

        /** @var StorageContainerMaterialHistory[] $history */
        $history = $this->apiGridManager->fetchData(
            $queryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier,
        );

        $responseDTO = $this->dtoFactory->createStorageContainerMaterialHistoryListResponseDTO(
            $history,
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
            'Storage Container Material History',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: StorageContainerMaterialHistoryDeleteResponseDTO::class
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
        '/warehouse/storage-container-material-history/{scmhId}',
        name: 'storage_container_material_history_delete',
        methods: ['DELETE']
    )]
    public function delete(string $scmhId): JsonResponse
    {
        $this->requireAdminUser();

        $history = $this->storageContainerMaterialHistoryRepository->find($scmhId);

        if ($history === null) {
            throw new NotFoundHttpException('Storage container material history not found');
        }

        $this->entityManager->remove($history);
        $this->entityManager->flush();

        $responseDTO = $this->dtoFactory->createStorageContainerMaterialHistoryDeleteResponseDTO();

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
