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
use App\Modules\Warehouse\DTO\WasteMaterialCreateRequestDTO;
use App\Modules\Warehouse\DTO\WasteMaterialCreateResponseDTO;
use App\Modules\Warehouse\DTO\WasteMaterialDeleteResponseDTO;
use App\Modules\Warehouse\DTO\WasteMaterialInfoResponseDTO;
use App\Modules\Warehouse\DTO\WasteMaterialListResponseDTO;
use App\Modules\Warehouse\DTO\WasteMaterialUpdateRequestDTO;
use App\Modules\Warehouse\DTO\WasteMaterialUpdateResponseDTO;
use App\Modules\Warehouse\Factory\DTOFactory;
use App\Modules\Warehouse\Grid\Enum\WasteMaterialGridSortEnum;
use App\Warehouse\DBAL\Enum\VolumeUnitEnum;
use App\Warehouse\DBAL\Repository\WasteMaterialRepository;
use App\Warehouse\WasteMaterialService;
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

class WasteMaterialController extends AbstractController
{
    private const string FILTER_CODE_KEY = 'code';
    private const string FILTER_LABEL_KEY = 'label';
    private const string FILTER_SHORT_LABEL_KEY = 'shortLabel';
    private const string FILTER_IS_ACTIVE_KEY = 'isActive';
    private const string FILTER_VOLUME_UNIT_KEY = 'volumeUnit';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly WasteMaterialRepository $wasteMaterialRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly WasteMaterialService $wasteMaterialService,
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
                    type: WasteMaterialCreateRequestDTO::class
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
                    type: WasteMaterialCreateResponseDTO::class
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
        '/warehouse/waste-materials',
        name: 'waste_material_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $wasteMaterialCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                WasteMaterialCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($wasteMaterialCreateRequestDTO);

            $wasteMaterial = $this->wasteMaterialService->createWasteMaterial(
                $wasteMaterialCreateRequestDTO->getCode(),
                $wasteMaterialCreateRequestDTO->getLabel(),
                $wasteMaterialCreateRequestDTO->getShortLabel(),
                $wasteMaterialCreateRequestDTO->getIsActive(),
                VolumeUnitEnum::from($wasteMaterialCreateRequestDTO->getVolumeUnit()),
            );

            $wasteMaterialCreateResponseDTO = $this->dtoFactory->createWasteMaterialCreateResponseDTO($wasteMaterial);

            return $this->json($wasteMaterialCreateResponseDTO);
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
                    type: WasteMaterialUpdateRequestDTO::class
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
                    type: WasteMaterialUpdateResponseDTO::class
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
        '/warehouse/waste-materials/{wasteMaterialId}',
        name: 'waste_material_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $wasteMaterialId): JsonResponse
    {
        $this->requireAdminUser();

        $wasteMaterial = $this->wasteMaterialRepository->find($wasteMaterialId);

        if ($wasteMaterial === null) {
            throw new NotFoundHttpException('Waste material not found');
        }

        try {
            $wasteMaterialUpdateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                WasteMaterialUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($wasteMaterialUpdateRequestDTO);

            $wasteMaterial = $this->wasteMaterialService->updateWasteMaterial(
                $wasteMaterial,
                $wasteMaterialUpdateRequestDTO->getCode(),
                $wasteMaterialUpdateRequestDTO->getLabel(),
                $wasteMaterialUpdateRequestDTO->getShortLabel(),
                $wasteMaterialUpdateRequestDTO->getIsActive(),
                VolumeUnitEnum::from($wasteMaterialUpdateRequestDTO->getVolumeUnit()),
            );

            $wasteMaterialUpdateResponseDTO = $this->dtoFactory->createWasteMaterialUpdateResponseDTO($wasteMaterial);

            return $this->json($wasteMaterialUpdateResponseDTO);
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
            'Warehouse',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: WasteMaterialInfoResponseDTO::class
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
        '/warehouse/waste-materials/{wasteMaterialId}',
        name: 'waste_material_info',
        methods: ['GET']
    )]
    public function info(string $wasteMaterialId): JsonResponse
    {
        $this->requireAdminUser();

        $wasteMaterial = $this->wasteMaterialRepository->find($wasteMaterialId);

        if ($wasteMaterial === null) {
            throw new NotFoundHttpException('Waste material not found');
        }

        $wasteMaterialInfoResponseDTO = $this->dtoFactory->createWasteMaterialInfoResponseDTO($wasteMaterial);

        return $this->json($wasteMaterialInfoResponseDTO);
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
                name: self::FILTER_CODE_KEY,
                description: 'Filter by code (strict)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'WM-01'
            ),
            new OA\Parameter(
                name: self::FILTER_LABEL_KEY,
                description: 'Filter by label (non-strict)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'Oil'
            ),
            new OA\Parameter(
                name: self::FILTER_SHORT_LABEL_KEY,
                description: 'Filter by short label (non-strict)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'Oil'
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
                    type: WasteMaterialListResponseDTO::class
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
        '/warehouse/waste-materials',
        name: 'waste_material_list',
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
                    $qb->expr()->eq(
                        WasteMaterialRepository::ALIAS . '.code',
                        ':code'
                    )
                );
                $qb->setParameter('code', $code);
            } catch (Throwable) {
                // pass
            }

            try {
                $label = $request->query->get(self::FILTER_LABEL_KEY);

                assert(is_string($label));

                $qb->andWhere(
                    $qb->expr()->like(
                        WasteMaterialRepository::ALIAS . '.label',
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
                        WasteMaterialRepository::ALIAS . '.shortLabel',
                        ':shortLabel'
                    )
                );
                $qb->setParameter('shortLabel', '%' . $shortLabel . '%');
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
                        WasteMaterialRepository::ALIAS . '.isActive',
                        ':isActive'
                    )
                );
                $qb->setParameter('isActive', $isActiveBool);
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
                            WasteMaterialRepository::ALIAS . '.volumeUnit',
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
            WasteMaterialGridSortEnum::class,
            WasteMaterialGridSortEnum::CODE
        );

        $wasteMaterialsQueryBuilder = $this->wasteMaterialRepository->getQueryBuilderWithAlias();
        $wasteMaterialsPaginator = $this->apiGridManager->createPaginator(
            $wasteMaterialsQueryBuilder,
            $queryModifier
        );

        $wasteMaterials = $this->apiGridManager->fetchData(
            $wasteMaterialsQueryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier,
        );

        $wasteMaterialListResponseDTO = $this->dtoFactory->createWasteMaterialListResponseDTO(
            $wasteMaterials,
            $this->apiGridPropertyHelper->createPageCount(
                $wasteMaterialsPaginator->count(),
                $maxResults
            )
        );

        return $this->json($wasteMaterialListResponseDTO);
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
                    type: WasteMaterialDeleteResponseDTO::class
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
        '/warehouse/waste-materials/{wasteMaterialId}',
        name: 'waste_material_delete',
        methods: ['DELETE']
    )]
    public function delete(string $wasteMaterialId): JsonResponse
    {
        $this->requireAdminUser();

        $wasteMaterial = $this->wasteMaterialRepository->find($wasteMaterialId);

        if ($wasteMaterial === null) {
            throw new NotFoundHttpException('Waste material not found');
        }

        try {
            $this->wasteMaterialService->deleteWasteMaterial($wasteMaterial);
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection($e->getErrorCollection());
        }

        $wasteMaterialDeleteResponseDTO = $this->dtoFactory->createWasteMaterialDeleteResponseDTO();

        return $this->json($wasteMaterialDeleteResponseDTO);
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
