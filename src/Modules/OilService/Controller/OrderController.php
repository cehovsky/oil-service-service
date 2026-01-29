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
use App\Modules\OilService\DTO\OrderCoordinatesResolveResponseDTO;
use App\Modules\OilService\DTO\OrderCoordinatesUpdateRequestDTO;
use App\Modules\OilService\DTO\OrderCreateWithTermRequestDTO;
use App\Modules\OilService\DTO\OrderDeleteResponseDTO;
use App\Modules\OilService\DTO\OrderInfoResponseDTO;
use App\Modules\OilService\DTO\OrderListResponseDTO;
use App\Modules\OilService\DTO\OrderInventoryItemUpdateItemDTO;
use App\Modules\OilService\DTO\OrderUpdateRequestDTO;
use App\Modules\OilService\DTO\OrderUpdateResponseDTO;
use App\Modules\OilService\DTO\OrderInventoryItemsUpdateRequestDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\Modules\OilService\Grid\Enum\OrderGridSortEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Entity\Route as RouteEntity;
use App\OilService\DBAL\Repository\OrderRepository;
use App\OilService\DBAL\Repository\RouteRepository;
use App\OilService\OrderInventoryItemService;
use App\OilService\OrderService;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\QueryBuilder;
use DateTimeImmutable;
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

class OrderController extends AbstractController
{
    private const string FILTER_IDENT_KEY = 'ident';
    private const string FILTER_FULL_NAME_KEY = 'fullName';
    private const string FILTER_EMAIL_KEY = 'email';
    private const string FILTER_PHONE_KEY = 'phone';
    private const string FILTER_CAR_MODEL_KEY = 'carModel';
    private const string FILTER_LICENSE_PLATE_KEY = 'licensePlate';
    private const string FILTER_IS_COMPANY_KEY = 'isCompany';
    private const string FILTER_USER_EMAIL_KEY = 'userEmail';
    private const string FILTER_STATUS_KEY = 'status';
    private const string FILTER_REALIZATION_TIME_SLOT_KEY = 'realizationTimeSlot';
    private const string FILTER_REALIZATION_DATE_KEY = 'realizationDate';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly OrderRepository $orderRepository,
        private readonly RouteRepository $routeRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly OrderService $orderService,
        private readonly OrderInventoryItemService $orderInventoryItemService,
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
                    type: OrderCreateWithTermRequestDTO::class
                ),
            )
        ),
        tags: [
            'Orders',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: OrderInfoResponseDTO::class
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
        '/oil-service/orders',
        name: 'oil_service_order_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $orderCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                OrderCreateWithTermRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($orderCreateRequestDTO);

            $route = $this->findRoute($orderCreateRequestDTO->getRouteId());

            $order = $this->orderService->createOrder(
                $orderCreateRequestDTO->getFullName(),
                $orderCreateRequestDTO->getPhone(),
                $orderCreateRequestDTO->getEmail(),
                $orderCreateRequestDTO->getCarModel(),
                $orderCreateRequestDTO->getLicensePlate(),
                $orderCreateRequestDTO->getAddress(),
                $orderCreateRequestDTO->getNote(),
                $orderCreateRequestDTO->getIsCompany(),
                $orderCreateRequestDTO->getCompanyName(),
                $orderCreateRequestDTO->getCompanyIdentificationNumber(),
                $orderCreateRequestDTO->getCompanyTaxId(),
                $orderCreateRequestDTO->getCompanyAddress(),
                $orderCreateRequestDTO->getOilChangeVehiclePhotoId(),
                $orderCreateRequestDTO->getVinPhotoId(),
                $orderCreateRequestDTO->getOldOilFilterPhotoId(),
                $orderCreateRequestDTO->getOldOilPhotoId(),
                $orderCreateRequestDTO->getOdometerPhotoId(),
                $orderCreateRequestDTO->getOtherPhotoIds(),
                OrderStatusEnum::from($orderCreateRequestDTO->getStatus()),
                RealizationTimeSlotEnum::from($orderCreateRequestDTO->getRealizationTimeSlot()),
                $this->createRealizationDate($orderCreateRequestDTO->getRealizationDate()),
                $orderCreateRequestDTO->getUserId(),
                $orderCreateRequestDTO->getPriceListItemIds(),
                $route,
            );

            $orderInfoResponseDTO = $this->dtoFactory->createOrderInfoResponseDTO($order);

            return $this->json($orderInfoResponseDTO);
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
                    type: OrderUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Orders',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: OrderUpdateResponseDTO::class
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
        '/oil-service/orders/{orderId}',
        name: 'oil_service_order_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $orderId): JsonResponse
    {
        $this->requireAdminUser();

        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            throw new NotFoundHttpException();
        }

        try {
            $orderUpdateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                OrderUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($orderUpdateRequestDTO);

            $routeProvided = $this->isFieldProvided($request, 'routeId');

            $order = $this->orderService->updateOrder(
                $order,
                $orderUpdateRequestDTO->getFullName(),
                $orderUpdateRequestDTO->getPhone(),
                $orderUpdateRequestDTO->getEmail(),
                $orderUpdateRequestDTO->getCarModel(),
                $orderUpdateRequestDTO->getLicensePlate(),
                $orderUpdateRequestDTO->getAddress(),
                $orderUpdateRequestDTO->getNote(),
                OrderStatusEnum::from($orderUpdateRequestDTO->getStatus()),
                RealizationTimeSlotEnum::from($orderUpdateRequestDTO->getRealizationTimeSlot()),
                $this->createRealizationDate($orderUpdateRequestDTO->getRealizationDate()),
                $orderUpdateRequestDTO->getIsCompany(),
                $orderUpdateRequestDTO->getCompanyName(),
                $orderUpdateRequestDTO->getCompanyIdentificationNumber(),
                $orderUpdateRequestDTO->getCompanyTaxId(),
                $orderUpdateRequestDTO->getCompanyAddress(),
                $orderUpdateRequestDTO->getOilChangeVehiclePhotoId(),
                $orderUpdateRequestDTO->getVinPhotoId(),
                $orderUpdateRequestDTO->getOldOilFilterPhotoId(),
                $orderUpdateRequestDTO->getOldOilPhotoId(),
                $orderUpdateRequestDTO->getOdometerPhotoId(),
                $orderUpdateRequestDTO->getOtherPhotoIds(),
                $orderUpdateRequestDTO->getUserId(),
                $routeProvided,
                $orderUpdateRequestDTO->getRouteId(),
                $orderUpdateRequestDTO->getPriceListItemIds(),
            );

            $orderUpdateResponseDTO = $this->dtoFactory->createOrderUpdateResponseDTO($order);

            return $this->json($orderUpdateResponseDTO);
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
                    type: OrderCoordinatesUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Orders',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: OrderUpdateResponseDTO::class
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
        '/oil-service/orders/{orderId}/coordinates',
        name: 'oil_service_order_coordinates_update',
        methods: ['PUT']
    )]
    public function updateCoordinates(Request $request, string $orderId): JsonResponse
    {
        $this->requireAdminUser();

        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            throw new NotFoundHttpException();
        }

        try {
            $coordinatesUpdateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                OrderCoordinatesUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($coordinatesUpdateRequestDTO);

            $order = $this->orderService->updateOrderCoordinates(
                $order,
                $coordinatesUpdateRequestDTO->getLatitude(),
                $coordinatesUpdateRequestDTO->getLongitude(),
            );

            $responseDTO = $this->dtoFactory->createOrderUpdateResponseDTO($order);

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
        tags: [
            'Orders',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: OrderCoordinatesResolveResponseDTO::class
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
        '/oil-service/orders/{orderId}/coordinates/resolve',
        name: 'oil_service_order_coordinates_resolve',
        methods: ['PUT']
    )]
    public function resolveCoordinates(string $orderId): JsonResponse
    {
        $this->requireAdminUser();

        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            throw new NotFoundHttpException();
        }

        try {
            $result = $this->orderService->resolveOrderCoordinatesFromAddress($order);

            $responseDTO = $this->dtoFactory->createOrderCoordinatesResolveResponseDTO(
                $order,
                $result->isSuccess(),
                $result->getMessage(),
            );

            return $this->json($responseDTO);
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
                    type: OrderInventoryItemsUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Orders',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: OrderUpdateResponseDTO::class
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
        '/oil-service/orders/{orderId}/inventory-items',
        name: 'oil_service_order_inventory_items_update',
        methods: ['PUT']
    )]
    public function updateInventoryItems(Request $request, string $orderId): JsonResponse
    {
        $user = $this->requireAdminUser();

        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            throw new NotFoundHttpException();
        }

        try {
            $updateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                OrderInventoryItemsUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($updateRequestDTO);

            $items = [];

            foreach ($updateRequestDTO->getItems() as $item) {
                if (!$item instanceof OrderInventoryItemUpdateItemDTO) {
                    throw new InvalidDataException();
                }

                $items[] = [
                    'inventoryItemId' => $item->getInventoryItemId(),
                    'quantity' => $item->getQuantity(),
                ];
            }

            $order = $this->orderInventoryItemService->updateOrderInventoryItems(
                $order,
                $items,
                $user,
            );

            $responseDTO = $this->dtoFactory->createOrderUpdateResponseDTO($order);

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
            'Orders',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: OrderInfoResponseDTO::class
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
        '/oil-service/orders/{orderId}',
        name: 'oil_service_order_info',
        methods: ['GET']
    )]
    public function info(string $orderId): JsonResponse
    {
        $this->requireAdminUser();

        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            throw new NotFoundHttpException();
        }

        $orderInfoResponseDTO = $this->dtoFactory->createOrderInfoResponseDTO($order);

        return $this->json($orderInfoResponseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Orders',
        ],
        parameters: [
            new OA\Parameter(
                name: self::FILTER_IDENT_KEY,
                description: 'Filter by ident (number or full format OYYXXXXX)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: '1'
            ),
            new OA\Parameter(
                name: self::FILTER_FULL_NAME_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'Jan Novák'
            ),
            new OA\Parameter(
                name: self::FILTER_EMAIL_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'jan.novak@example.com'
            ),
            new OA\Parameter(
                name: self::FILTER_PHONE_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: '+420 123 456 789'
            ),
            new OA\Parameter(
                name: self::FILTER_CAR_MODEL_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'Škoda'
            ),
            new OA\Parameter(
                name: self::FILTER_LICENSE_PLATE_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: '1A2 3456'
            ),
            new OA\Parameter(
                name: self::FILTER_IS_COMPANY_KEY,
                description: 'strict filtered',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'boolean'
                ),
                example: true
            ),
            new OA\Parameter(
                name: self::FILTER_USER_EMAIL_KEY,
                description: 'non-strict filtering by user email',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'jan.novak@example.com'
            ),
            new OA\Parameter(
                name: self::FILTER_STATUS_KEY,
                description: 'Filter by status',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: OrderStatusEnum::VALUES
                ),
                example: 'new'
            ),
            new OA\Parameter(
                name: self::FILTER_REALIZATION_TIME_SLOT_KEY,
                description: 'Filter by realization time slot',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: RealizationTimeSlotEnum::VALUES
                ),
                example: 'morning'
            ),
            new OA\Parameter(
                name: self::FILTER_REALIZATION_DATE_KEY,
                description: 'Filter by realization date (YYYY-MM-DD)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: '2025-01-15'
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
                    type: OrderListResponseDTO::class
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/oil-service/orders',
        name: 'oil_service_order_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            // Join user for filtering
            $qb->leftJoin(OrderRepository::ALIAS . '.user', 'u');

            try {
                $ident = $request->query->get(self::FILTER_IDENT_KEY);

                assert(is_string($ident));

                // Parse ident - can be number or formatted OYYXXXXX
                if (preg_match('/^[Oo](\d{2})(\d+)$/', $ident, $matches)) {
                    $identNumber = (int) $matches[2];
                } elseif (is_numeric($ident)) {
                    $identNumber = (int) $ident;
                } else {
                    $identNumber = null;
                }

                if ($identNumber !== null) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            OrderRepository::ALIAS . '.ident',
                            ':ident'
                        )
                    );
                    $qb->setParameter('ident', $identNumber);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $fullName = $request->query->get(self::FILTER_FULL_NAME_KEY);

                assert(is_string($fullName));

                $qb->andWhere(
                    $qb->expr()->like(
                        OrderRepository::ALIAS . '.fullName',
                        ':fullName'
                    )
                );
                $qb->setParameter('fullName', '%' . $fullName . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $email = $request->query->get(self::FILTER_EMAIL_KEY);

                assert(is_string($email));

                $qb->andWhere(
                    $qb->expr()->like(
                        OrderRepository::ALIAS . '.email',
                        ':email'
                    )
                );
                $qb->setParameter('email', '%' . $email . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $phone = $request->query->get(self::FILTER_PHONE_KEY);

                assert(is_string($phone));

                $qb->andWhere(
                    $qb->expr()->like(
                        OrderRepository::ALIAS . '.phone',
                        ':phone'
                    )
                );
                $qb->setParameter('phone', '%' . $phone . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $carModel = $request->query->get(self::FILTER_CAR_MODEL_KEY);

                assert(is_string($carModel));

                $qb->andWhere(
                    $qb->expr()->like(
                        OrderRepository::ALIAS . '.carModel',
                        ':carModel'
                    )
                );
                $qb->setParameter('carModel', '%' . $carModel . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $licensePlate = $request->query->get(self::FILTER_LICENSE_PLATE_KEY);

                assert(is_string($licensePlate));

                $qb->andWhere(
                    $qb->expr()->like(
                        OrderRepository::ALIAS . '.licensePlate',
                        ':licensePlate'
                    )
                );
                $qb->setParameter('licensePlate', '%' . $licensePlate . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $isCompanyRaw = $request->query->get(self::FILTER_IS_COMPANY_KEY);

                assert($isCompanyRaw !== null);

                $isCompany = filter_var($isCompanyRaw, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

                assert($isCompany !== null);

                $qb->andWhere(
                    $qb->expr()->eq(
                        OrderRepository::ALIAS . '.isCompany',
                        ':isCompany'
                    )
                );
                $qb->setParameter('isCompany', $isCompany);
            } catch (Throwable) {
                // pass
            }

            try {
                $userEmail = $request->query->get(self::FILTER_USER_EMAIL_KEY);

                assert(is_string($userEmail));

                $qb->andWhere(
                    $qb->expr()->like(
                        'u.email',
                        ':userEmail'
                    )
                );
                $qb->setParameter('userEmail', '%' . $userEmail . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $status = $request->query->get(self::FILTER_STATUS_KEY);

                assert(is_string($status));

                $statusEnum = OrderStatusEnum::tryFrom($status);

                if ($statusEnum !== null) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            OrderRepository::ALIAS . '.status',
                            ':status'
                        )
                    );
                    $qb->setParameter('status', $statusEnum->value);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $realizationTimeSlot = $request->query->get(self::FILTER_REALIZATION_TIME_SLOT_KEY);

                assert(is_string($realizationTimeSlot));

                $realizationTimeSlotEnum = RealizationTimeSlotEnum::tryFrom($realizationTimeSlot);

                if ($realizationTimeSlotEnum !== null) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            OrderRepository::ALIAS . '.realizationTimeSlot',
                            ':realizationTimeSlot'
                        )
                    );
                    $qb->setParameter('realizationTimeSlot', $realizationTimeSlotEnum->value);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $realizationDateRaw = $request->query->get(self::FILTER_REALIZATION_DATE_KEY);

                assert(is_string($realizationDateRaw));

                $realizationDate = DateTimeImmutable::createFromFormat('!Y-m-d', $realizationDateRaw);

                if ($realizationDate !== false) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            OrderRepository::ALIAS . '.realizationDate',
                            ':realizationDate'
                        )
                    );
                    $qb->setParameter('realizationDate', $realizationDate, Types::DATE_IMMUTABLE);
                }
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::DESC);
        $orderGridSortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            OrderGridSortEnum::class,
            OrderGridSortEnum::CREATED_AT
        );
        $ordersQueryBuilder = $this->orderRepository->getQueryBuilderWithAlias();
        $ordersPaginator = $this->apiGridManager->createPaginator(
            $ordersQueryBuilder,
            $queryModifier
        );
        /** @var Order[] $orders */
        $orders = $this->apiGridManager->fetchData(
            $ordersQueryBuilder,
            $orderGridSortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );
        $orderListResponseDTO = $this->dtoFactory->createOrderListResponseDTO(
            $orders,
            $this->apiGridPropertyHelper->createPageCount(
                $ordersPaginator->count(),
                $maxResults
            )
        );

        return $this->json($orderListResponseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Dashboard',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Last 10 orders without filters',
                content: new Model(
                    type: OrderListResponseDTO::class
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
        '/oil-service/dashboard/orders/recent',
        name: 'oil_service_dashboard_order_recent',
        methods: ['GET']
    )]
    public function listRecent(): JsonResponse
    {
        $this->requireAdminUser();

        $qb = $this->orderRepository->createQueryBuilder(OrderRepository::ALIAS);

        $qb->orderBy(OrderRepository::ALIAS . '.createdAt', 'DESC')
            ->setMaxResults(10);

        /** @var Order[] $orders */
        $orders = $qb->getQuery()->getResult();

        $orderListResponseDTO = $this->dtoFactory->createOrderListResponseDTO($orders, 1);

        return $this->json($orderListResponseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Orders',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: OrderDeleteResponseDTO::class
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
        '/oil-service/orders/{orderId}',
        name: 'oil_service_order_delete',
        methods: ['DELETE']
    )]
    public function delete(string $orderId): JsonResponse
    {
        $this->requireAdminUser();

        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            throw new NotFoundHttpException();
        }

        $this->orderService->deleteOrder($order);

        $orderDeleteResponseDTO = $this->dtoFactory->createOrderDeleteResponseDTO();

        return $this->json($orderDeleteResponseDTO);
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

    private function findRoute(?string $routeId): ?RouteEntity
    {
        if ($routeId === null) {
            return null;
        }

        $route = $this->routeRepository->find($routeId);

        if ($route === null) {
            throw new NotFoundHttpException();
        }

        return $route;
    }

    private function isFieldProvided(Request $request, string $field): bool
    {
        try {
            $data = $request->toArray();

            return array_key_exists($field, $data);
        } catch (Throwable) {
            return false;
        }
    }

    private function createRealizationDate(string $realizationDate): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $realizationDate);

        if ($date === false) {
            throw new InvalidDataException('Invalid realization date format.');
        }

        return $date;
    }
}
