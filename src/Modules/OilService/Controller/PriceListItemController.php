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
use App\Modules\OilService\DTO\PriceListItemCreateRequestDTO;
use App\Modules\OilService\DTO\PriceListItemCreateResponseDTO;
use App\Modules\OilService\DTO\PriceListItemDeleteResponseDTO;
use App\Modules\OilService\DTO\PriceListItemInfoResponseDTO;
use App\Modules\OilService\DTO\PriceListItemListResponseDTO;
use App\Modules\OilService\DTO\PriceListItemUpdateRequestDTO;
use App\Modules\OilService\DTO\PriceListItemUpdateResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\Modules\OilService\Grid\Enum\PriceListItemGridSortEnum;
use App\OilService\DBAL\Entity\PriceListItem;
use App\OilService\DBAL\Repository\PriceListItemRepository;
use App\OilService\PriceListItemService;
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

class PriceListItemController extends AbstractController
{
    private const string FILTER_LABEL_KEY = 'label';
    private const string FILTER_DESCRIPTION_KEY = 'description';
    private const string FILTER_INVOICE_LABEL_KEY = 'invoiceLabel';
    private const string FILTER_CODE_KEY = 'code';
    private const string FILTER_BRAND_KEY = 'brand';
    private const string FILTER_EXTERNAL_CODE_KEY = 'externalCode';
    private const string FILTER_PRICE_KEY = 'price';
    private const string FILTER_VAT_KEY = 'vat';
    private const string FILTER_PRICE_VAT_KEY = 'priceVat';
    private const string FILTER_IS_ACTIVE_KEY = 'isActive';
    private const string FILTER_IS_PUBLIC_KEY = 'isPublic';
    private const string FILTER_IS_DEFAULT_KEY = 'isDefault';
    private const string FILTER_IS_HIDDEN_ON_INVOICE_KEY = 'isHiddenOnInvoice';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly PriceListItemRepository $priceListItemRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly PriceListItemService $priceListItemService,
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
                    type: PriceListItemCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'PriceListItems',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: PriceListItemCreateResponseDTO::class
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
        '/oil-service/price-list-items',
        name: 'oil_service_price_list_item_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $createRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                PriceListItemCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($createRequestDTO);

            $priceListItem = $this->priceListItemService->createPriceListItem(
                $createRequestDTO->getLabel(),
                $createRequestDTO->getDescription(),
                $createRequestDTO->getInvoiceLabel(),
                $createRequestDTO->getPriceVat(),
                $createRequestDTO->getVat(),
                $createRequestDTO->getIsActive(),
                $createRequestDTO->getIsPublic(),
                $createRequestDTO->getIsDefault(),
                $createRequestDTO->getIsHiddenOnInvoice(),
                $createRequestDTO->getCode(),
                $createRequestDTO->getBrand(),
                $createRequestDTO->getExternalCode(),
            );

            $responseDTO = $this->dtoFactory->createPriceListItemCreateResponseDTO($priceListItem);

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
                    type: PriceListItemUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'PriceListItems',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: PriceListItemUpdateResponseDTO::class
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
        '/oil-service/price-list-items/{priceListItemId}',
        name: 'oil_service_price_list_item_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $priceListItemId): JsonResponse
    {
        $this->requireAdminUser();

        $priceListItem = $this->priceListItemRepository->find($priceListItemId);

        if ($priceListItem === null) {
            throw new NotFoundHttpException();
        }

        try {
            $updateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                PriceListItemUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($updateRequestDTO);

            $priceListItem = $this->priceListItemService->updatePriceListItem(
                $priceListItem,
                $updateRequestDTO->getLabel(),
                $updateRequestDTO->getDescription(),
                $updateRequestDTO->getInvoiceLabel(),
                $updateRequestDTO->getPriceVat(),
                $updateRequestDTO->getVat(),
                $updateRequestDTO->getIsActive(),
                $updateRequestDTO->getIsPublic(),
                $updateRequestDTO->getIsDefault(),
                $updateRequestDTO->getIsHiddenOnInvoice(),
                $updateRequestDTO->getCode(),
                $updateRequestDTO->getBrand(),
                $updateRequestDTO->getExternalCode(),
            );

            $responseDTO = $this->dtoFactory->createPriceListItemUpdateResponseDTO($priceListItem);

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

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'PriceListItems',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: PriceListItemInfoResponseDTO::class
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
        '/oil-service/price-list-items/{priceListItemId}',
        name: 'oil_service_price_list_item_info',
        methods: ['GET']
    )]
    public function info(string $priceListItemId): JsonResponse
    {
        $this->requireAdminUser();

        $priceListItem = $this->priceListItemRepository->find($priceListItemId);

        if ($priceListItem === null) {
            throw new NotFoundHttpException();
        }

        $responseDTO = $this->dtoFactory->createPriceListItemInfoResponseDTO($priceListItem);

        return $this->json($responseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'PriceListItems',
        ],
        parameters: [
            new OA\Parameter(
                name: self::FILTER_LABEL_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'Oil change'
            ),
            new OA\Parameter(
                name: self::FILTER_DESCRIPTION_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'Standard'
            ),
            new OA\Parameter(
                name: self::FILTER_INVOICE_LABEL_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'Oil change service'
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
                name: self::FILTER_BRAND_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'Shell'
            ),
            new OA\Parameter(
                name: self::FILTER_EXTERNAL_CODE_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'EXT-001'
            ),
            new OA\Parameter(
                name: self::FILTER_PRICE_KEY,
                description: 'Filter by price (without VAT)',
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
                name: self::FILTER_IS_ACTIVE_KEY,
                description: 'Filter by isActive',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean'),
                example: true
            ),
            new OA\Parameter(
                name: self::FILTER_IS_PUBLIC_KEY,
                description: 'Filter by isPublic',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean'),
                example: true
            ),
            new OA\Parameter(
                name: self::FILTER_IS_DEFAULT_KEY,
                description: 'Filter by isDefault',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean'),
                example: false
            ),
            new OA\Parameter(
                name: self::FILTER_IS_HIDDEN_ON_INVOICE_KEY,
                description: 'Filter by isHiddenOnInvoice',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'boolean'),
                example: false
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: PriceListItemListResponseDTO::class
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
        '/oil-service/price-list-items',
        name: 'oil_service_price_list_item_list',
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
                        PriceListItemRepository::ALIAS . '.label',
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
                        PriceListItemRepository::ALIAS . '.description',
                        ':description'
                    )
                );
                $qb->setParameter('description', '%' . $description . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $invoiceLabel = $request->query->get(self::FILTER_INVOICE_LABEL_KEY);

                assert(is_string($invoiceLabel));

                $qb->andWhere(
                    $qb->expr()->like(
                        PriceListItemRepository::ALIAS . '.invoiceLabel',
                        ':invoiceLabel'
                    )
                );
                $qb->setParameter('invoiceLabel', '%' . $invoiceLabel . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $code = $request->query->get(self::FILTER_CODE_KEY);

                assert(is_string($code));

                $qb->andWhere(
                    $qb->expr()->eq(
                        PriceListItemRepository::ALIAS . '.code',
                        ':code'
                    )
                );
                $qb->setParameter('code', $code);
            } catch (Throwable) {
                // pass
            }

            try {
                $brand = $request->query->get(self::FILTER_BRAND_KEY);

                assert(is_string($brand));

                $qb->andWhere(
                    $qb->expr()->like(
                        PriceListItemRepository::ALIAS . '.brand',
                        ':brand'
                    )
                );
                $qb->setParameter('brand', '%' . $brand . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $externalCode = $request->query->get(self::FILTER_EXTERNAL_CODE_KEY);

                assert(is_string($externalCode));

                $qb->andWhere(
                    $qb->expr()->like(
                        PriceListItemRepository::ALIAS . '.externalCode',
                        ':externalCode'
                    )
                );
                $qb->setParameter('externalCode', '%' . $externalCode . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $price = $request->query->get(self::FILTER_PRICE_KEY);

                assert(is_string($price));

                $priceValue = QueryParameterParser::parseNumeric($price);

                $qb->andWhere(
                    $qb->expr()->eq(
                        PriceListItemRepository::ALIAS . '.price',
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
                        PriceListItemRepository::ALIAS . '.vat',
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
                        PriceListItemRepository::ALIAS . '.priceVat',
                        ':priceVat'
                    )
                );
                $qb->setParameter('priceVat', $priceVatValue);
            } catch (Throwable) {
                // pass
            }

            $this->applyBooleanFilter($request, $qb, self::FILTER_IS_ACTIVE_KEY, 'isActive');
            $this->applyBooleanFilter($request, $qb, self::FILTER_IS_PUBLIC_KEY, 'isPublic');
            $this->applyBooleanFilter($request, $qb, self::FILTER_IS_DEFAULT_KEY, 'isDefault');
            $this->applyBooleanFilter($request, $qb, self::FILTER_IS_HIDDEN_ON_INVOICE_KEY, 'isHiddenOnInvoice');
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::ASC);
        $priceListItemSortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            PriceListItemGridSortEnum::class,
            PriceListItemGridSortEnum::LABEL
        );
        $priceListItemsQueryBuilder = $this->priceListItemRepository->getQueryBuilderWithAlias();
        $priceListItemsPaginator = $this->apiGridManager->createPaginator(
            $priceListItemsQueryBuilder,
            $queryModifier
        );
        /** @var PriceListItem[] $priceListItems */
        $priceListItems = $this->apiGridManager->fetchData(
            $priceListItemsQueryBuilder,
            $priceListItemSortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );
        $responseDTO = $this->dtoFactory->createPriceListItemListResponseDTO(
            $priceListItems,
            $this->apiGridPropertyHelper->createPageCount(
                $priceListItemsPaginator->count(),
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
            'PriceListItems',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: PriceListItemDeleteResponseDTO::class
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
        '/oil-service/price-list-items/{priceListItemId}',
        name: 'oil_service_price_list_item_delete',
        methods: ['DELETE']
    )]
    public function delete(string $priceListItemId): JsonResponse
    {
        $this->requireAdminUser();

        $priceListItem = $this->priceListItemRepository->find($priceListItemId);

        if ($priceListItem === null) {
            throw new NotFoundHttpException();
        }

        $this->priceListItemService->deletePriceListItem($priceListItem);

        $responseDTO = $this->dtoFactory->createPriceListItemDeleteResponseDTO();

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

    private function applyBooleanFilter(Request $request, QueryBuilder $qb, string $filterKey, string $field): void
    {
        try {
            $value = $request->query->get($filterKey);

            $boolValue = QueryParameterParser::parseBoolean($value);

            $qb->andWhere(
                $qb->expr()->eq(
                    PriceListItemRepository::ALIAS . '.' . $field,
                    ':' . $filterKey
                )
            );
            $qb->setParameter($filterKey, $boolValue);
        } catch (Throwable) {
            // pass
        }
    }
}
