<?php

declare(strict_types=1);

namespace App\Modules\OilService\Controller;

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
use App\Modules\OilService\DTO\InventoryItemCreateRequestDTO;
use App\Modules\OilService\DTO\InventoryItemCreateResponseDTO;
use App\Modules\OilService\DTO\InventoryItemDeleteResponseDTO;
use App\Modules\OilService\DTO\InventoryItemInfoResponseDTO;
use App\Modules\OilService\DTO\InventoryItemListResponseDTO;
use App\Modules\OilService\DTO\InventoryItemStockInRequestDTO;
use App\Modules\OilService\DTO\InventoryItemStockInResponseDTO;
use App\Modules\OilService\DTO\InventoryItemStockOutRequestDTO;
use App\Modules\OilService\DTO\InventoryItemStockOutResponseDTO;
use App\Modules\OilService\DTO\InventoryItemUpdateRequestDTO;
use App\Modules\OilService\DTO\InventoryItemUpdateResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\Modules\OilService\Grid\Enum\InventoryItemGridSortEnum;
use App\OilService\DBAL\Entity\InventoryItem;
use App\OilService\DBAL\Repository\InventoryItemRepository;
use App\OilService\InventoryItemService;
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

class InventoryItemController extends AbstractController
{
    private const string FILTER_LABEL_KEY = 'label';
    private const string FILTER_DESCRIPTION_KEY = 'description';
    private const string FILTER_CODE_KEY = 'code';
    private const string FILTER_OEM_CODE_KEY = 'oemCode';
    private const string FILTER_PRICE_KEY = 'price';
    private const string FILTER_VAT_KEY = 'vat';
    private const string FILTER_PRICE_VAT_KEY = 'priceVat';
    private const string FILTER_STOCK_COUNT_KEY = 'stockCount';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly InventoryItemRepository $inventoryItemRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly InventoryItemService $inventoryItemService,
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
                ref: new Model(type: InventoryItemCreateRequestDTO::class)
            )
        ),
        tags: [
            'InventoryItems',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(type: InventoryItemCreateResponseDTO::class)
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new Model(type: ErrorCollection::class)
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
        '/oil-service/inventory-items',
        name: 'oil_service_inventory_item_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $user = $this->requireAdminUser();

        try {
            $createRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                InventoryItemCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($createRequestDTO);

            $inventoryItem = $this->inventoryItemService->createInventoryItem(
                $createRequestDTO->getLabel(),
                $createRequestDTO->getDescription(),
                $createRequestDTO->getCode(),
                $createRequestDTO->getOemCode(),
                $createRequestDTO->getPrice(),
                $createRequestDTO->getVat(),
                $user,
            );

            $responseDTO = $this->dtoFactory->createInventoryItemCreateResponseDTO($inventoryItem);

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
                ref: new Model(type: InventoryItemUpdateRequestDTO::class)
            )
        ),
        tags: [
            'InventoryItems',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(type: InventoryItemUpdateResponseDTO::class)
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new Model(type: ErrorCollection::class)
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
        '/oil-service/inventory-items/{inventoryItemId}',
        name: 'oil_service_inventory_item_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $inventoryItemId): JsonResponse
    {
        $user = $this->requireAdminUser();

        $inventoryItem = $this->inventoryItemRepository->find($inventoryItemId);

        if ($inventoryItem === null) {
            throw new NotFoundHttpException();
        }

        try {
            $updateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                InventoryItemUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($updateRequestDTO);

            $inventoryItem = $this->inventoryItemService->updateInventoryItem(
                $inventoryItem,
                $updateRequestDTO->getLabel(),
                $updateRequestDTO->getDescription(),
                $updateRequestDTO->getCode(),
                $updateRequestDTO->getOemCode(),
                $updateRequestDTO->getPrice(),
                $updateRequestDTO->getVat(),
                $user,
            );

            $responseDTO = $this->dtoFactory->createInventoryItemUpdateResponseDTO($inventoryItem);

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
            'InventoryItems',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(type: InventoryItemInfoResponseDTO::class)
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
        '/oil-service/inventory-items/{inventoryItemId}',
        name: 'oil_service_inventory_item_info',
        methods: ['GET']
    )]
    public function info(string $inventoryItemId): JsonResponse
    {
        $this->requireAdminUser();

        $inventoryItem = $this->inventoryItemRepository->find($inventoryItemId);

        if ($inventoryItem === null) {
            throw new NotFoundHttpException();
        }

        $responseDTO = $this->dtoFactory->createInventoryItemInfoResponseDTO($inventoryItem);

        return $this->json($responseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'InventoryItems',
        ],
        parameters: [
            new OA\Parameter(
                name: self::FILTER_LABEL_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'filter'
            ),
            new OA\Parameter(
                name: self::FILTER_DESCRIPTION_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'filter'
            ),
            new OA\Parameter(
                name: self::FILTER_CODE_KEY,
                description: 'Filter by code',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: '1001'
            ),
            new OA\Parameter(
                name: self::FILTER_OEM_CODE_KEY,
                description: 'Filter by OEM code (contains)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'OEM-001'
            ),
            new OA\Parameter(
                name: self::FILTER_PRICE_KEY,
                description: 'Filter by price',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: '1200.00'
            ),
            new OA\Parameter(
                name: self::FILTER_VAT_KEY,
                description: 'Filter by VAT',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 21
            ),
            new OA\Parameter(
                name: self::FILTER_PRICE_VAT_KEY,
                description: 'Filter by price with VAT',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: '1452.00'
            ),
            new OA\Parameter(
                name: self::FILTER_STOCK_COUNT_KEY,
                description: 'Filter by stock count',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: 10
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
                description: 'Number of items on the page, default value ' . ApiGridPropertyHelper::MAX_RESULTS_DEFAULT_VALUE,
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: ApiGridPropertyHelper::MAX_RESULTS_DEFAULT_VALUE
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::SORT_KEY,
                description: 'Sorting by values, default value label',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'label'
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::ORDER_KEY,
                description: 'Select ordering, default value ASC, values: ASC, DESC',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'ASC'
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(type: InventoryItemListResponseDTO::class)
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
        '/oil-service/inventory-items',
        name: 'oil_service_inventory_item_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            try {
                $label = $request->query->get(self::FILTER_LABEL_KEY);

                assert(is_string($label));

                $qb->andWhere(
                    $qb->expr()->like(
                        InventoryItemRepository::ALIAS . '.label',
                        ':label'
                    )
                );
                $qb->setParameter('label', '%' . $label . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $description = $request->query->get(self::FILTER_DESCRIPTION_KEY);

                assert(is_string($description));

                $qb->andWhere(
                    $qb->expr()->like(
                        InventoryItemRepository::ALIAS . '.description',
                        ':description'
                    )
                );
                $qb->setParameter('description', '%' . $description . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $code = $request->query->get(self::FILTER_CODE_KEY);

                assert(is_string($code));

                $qb->andWhere(
                    $qb->expr()->eq(
                        InventoryItemRepository::ALIAS . '.code',
                        ':code'
                    )
                );
                $qb->setParameter('code', $code);
            } catch (Throwable) {
                // pass
            }

            try {
                $oemCode = $request->query->get(self::FILTER_OEM_CODE_KEY);

                assert(is_string($oemCode));

                $qb->andWhere(
                    $qb->expr()->like(
                        InventoryItemRepository::ALIAS . '.oemCode',
                        ':oemCode'
                    )
                );
                $qb->setParameter('oemCode', '%' . $oemCode . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $price = $request->query->get(self::FILTER_PRICE_KEY);

                assert(is_string($price));

                $priceValue = QueryParameterParser::parseNumeric($price);

                $qb->andWhere(
                    $qb->expr()->eq(
                        InventoryItemRepository::ALIAS . '.price',
                        ':price'
                    )
                );
                $qb->setParameter('price', $priceValue);
            } catch (Throwable) {
                // pass
            }

            try {
                $vat = $request->query->get(self::FILTER_VAT_KEY);

                assert(is_string($vat));

                $vatValue = QueryParameterParser::parseInteger($vat);

                $qb->andWhere(
                    $qb->expr()->eq(
                        InventoryItemRepository::ALIAS . '.vat',
                        ':vat'
                    )
                );
                $qb->setParameter('vat', $vatValue);
            } catch (Throwable) {
                // pass
            }

            try {
                $priceVat = $request->query->get(self::FILTER_PRICE_VAT_KEY);

                assert(is_string($priceVat));

                $priceVatValue = QueryParameterParser::parseNumeric($priceVat);

                $qb->andWhere(
                    $qb->expr()->eq(
                        InventoryItemRepository::ALIAS . '.priceVat',
                        ':priceVat'
                    )
                );
                $qb->setParameter('priceVat', $priceVatValue);
            } catch (Throwable) {
                // pass
            }

            try {
                $stockCount = $request->query->get(self::FILTER_STOCK_COUNT_KEY);

                assert(is_string($stockCount));

                $stockCountValue = QueryParameterParser::parseInteger($stockCount);

                $qb->andWhere(
                    $qb->expr()->eq(
                        InventoryItemRepository::ALIAS . '.stockCount',
                        ':stockCount'
                    )
                );
                $qb->setParameter('stockCount', $stockCountValue);
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::ASC);
        $sortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            InventoryItemGridSortEnum::class,
            InventoryItemGridSortEnum::LABEL
        );
        $inventoryItemsQueryBuilder = $this->inventoryItemRepository->getQueryBuilderWithAlias();
        $inventoryItemsPaginator = $this->apiGridManager->createPaginator(
            $inventoryItemsQueryBuilder,
            $queryModifier
        );
        /** @var InventoryItem[] $inventoryItems */
        $inventoryItems = $this->apiGridManager->fetchData(
            $inventoryItemsQueryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );
        $responseDTO = $this->dtoFactory->createInventoryItemListResponseDTO(
            $inventoryItems,
            $this->apiGridPropertyHelper->createPageCount(
                $inventoryItemsPaginator->count(),
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
            'InventoryItems',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(type: InventoryItemDeleteResponseDTO::class)
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
        '/oil-service/inventory-items/{inventoryItemId}',
        name: 'oil_service_inventory_item_delete',
        methods: ['DELETE']
    )]
    public function delete(string $inventoryItemId): JsonResponse
    {
        $this->requireAdminUser();

        $inventoryItem = $this->inventoryItemRepository->find($inventoryItemId);

        if ($inventoryItem === null) {
            throw new NotFoundHttpException();
        }

        $this->inventoryItemService->deleteInventoryItem($inventoryItem);

        $responseDTO = $this->dtoFactory->createInventoryItemDeleteResponseDTO();

        return $this->json($responseDTO);
    }

    #[OA\Post(
        security: [
            [
                'Bearer' => []
            ],
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(type: InventoryItemStockInRequestDTO::class)
            )
        ),
        tags: [
            'InventoryItems',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Stock in',
                content: new Model(type: InventoryItemStockInResponseDTO::class)
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new Model(type: ErrorCollection::class)
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
        '/oil-service/inventory-items/{inventoryItemId}/stock-in',
        name: 'oil_service_inventory_item_stock_in',
        methods: ['POST']
    )]
    public function stockIn(Request $request, string $inventoryItemId): JsonResponse
    {
        $user = $this->requireAdminUser();

        $inventoryItem = $this->inventoryItemRepository->find($inventoryItemId);

        if ($inventoryItem === null) {
            throw new NotFoundHttpException();
        }

        try {
            $stockInDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                InventoryItemStockInRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($stockInDTO);

            $inventoryItem = $this->inventoryItemService->stockIn(
                $inventoryItem,
                $stockInDTO->getQuantity(),
                $stockInDTO->getOrderId(),
                $stockInDTO->getPrice(),
                $stockInDTO->getVat(),
                $stockInDTO->getNote(),
                $user,
            );

            $responseDTO = $this->dtoFactory->createInventoryItemStockInResponseDTO($inventoryItem);

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
                ref: new Model(type: InventoryItemStockOutRequestDTO::class)
            )
        ),
        tags: [
            'InventoryItems',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Stock out',
                content: new Model(type: InventoryItemStockOutResponseDTO::class)
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new Model(type: ErrorCollection::class)
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
        '/oil-service/inventory-items/{inventoryItemId}/stock-out',
        name: 'oil_service_inventory_item_stock_out',
        methods: ['POST']
    )]
    public function stockOut(Request $request, string $inventoryItemId): JsonResponse
    {
        $user = $this->requireAdminUser();

        $inventoryItem = $this->inventoryItemRepository->find($inventoryItemId);

        if ($inventoryItem === null) {
            throw new NotFoundHttpException();
        }

        try {
            $stockOutDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                InventoryItemStockOutRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($stockOutDTO);

            $inventoryItem = $this->inventoryItemService->stockOut(
                $inventoryItem,
                $stockOutDTO->getQuantity(),
                $stockOutDTO->getOrderId(),
                $stockOutDTO->getNote(),
                $user,
            );

            $responseDTO = $this->dtoFactory->createInventoryItemStockOutResponseDTO($inventoryItem);

            return $this->json($responseDTO);
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection($e->getErrorCollection());
        } catch (InvalidDataException) {
            throw new BadRequestHttpException();
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
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
