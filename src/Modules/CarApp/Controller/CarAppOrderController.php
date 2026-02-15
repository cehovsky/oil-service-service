<?php

declare(strict_types=1);

namespace App\Modules\CarApp\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Domain\DTOValueResolver;
use App\OilService\OrderAccessService;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Modules\CarApp\DTO\OrderPhotosUpdateRequestDTO;
use App\Modules\OilService\DTO\OrderInventoryItemUpdateItemDTO;
use App\Modules\OilService\DTO\OrderInventoryItemsUpdateRequestDTO;
use App\Modules\OilService\DTO\OrderUpdateResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\OilService\DBAL\Entity\Order;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\DBAL\Repository\OrderRepository;
use App\OilService\OrderInventoryItemService;
use App\OilService\OrderService;
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

class CarAppOrderController extends AbstractController
{
    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly OrderRepository $orderRepository,
        private readonly OrderInventoryItemService $orderInventoryItemService,
        private readonly OrderService $orderService,
        private readonly Security $security,
        private readonly OrderAccessService $orderAccessService,
    ) {
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
            'Car App Orders',
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
        '/car-app/orders/{orderId}/inventory-items',
        name: 'oil_service_car_app_order_inventory_items_update',
        methods: ['PUT']
    )]
    public function updateInventoryItems(Request $request, string $orderId): JsonResponse
    {
        $user = $this->requireActiveUser();

        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            throw new NotFoundHttpException();
        }

        $this->orderAccessService->assertUserHasAccessToOrder($order, $user);

        try {
            $updateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                OrderInventoryItemsUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($updateRequestDTO);

            $items = [];

            foreach ($updateRequestDTO->getItems() as $item) {
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

    #[OA\Put(
        security: [
            [
                'Bearer' => []
            ],
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: OrderPhotosUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Car App Orders',
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
        '/car-app/orders/{orderId}/photos',
        name: 'oil_service_car_app_order_photos_update',
        methods: ['PUT']
    )]
    public function updatePhotos(Request $request, string $orderId): JsonResponse
    {
        $user = $this->requireActiveUser();

        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            throw new NotFoundHttpException();
        }

        $this->orderAccessService->assertUserHasAccessToOrder($order, $user);

        try {
            $updateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                OrderPhotosUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($updateRequestDTO);

            $order = $this->orderService->updateOrderPhotos(
                $order,
                $updateRequestDTO->getOilChangeVehiclePhotoId(),
                $updateRequestDTO->getVinPhotoId(),
                $updateRequestDTO->getOldOilFilterPhotoId(),
                $updateRequestDTO->getOldOilPhotoId(),
                $updateRequestDTO->getOdometerPhotoId(),
                $updateRequestDTO->getMileage(),
                $updateRequestDTO->getOtherPhotoIds(),
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
            'Car App Orders',
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
        '/car-app/orders/{orderId}/status/in-process',
        name: 'oil_service_car_app_order_status_in_process',
        methods: ['PUT']
    )]
    public function markInProcess(string $orderId): JsonResponse
    {
        return $this->updateStatus($orderId, OrderStatusEnum::IN_PROCESS);
    }

    #[OA\Put(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Car App Orders',
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
        '/car-app/orders/{orderId}/status/completed',
        name: 'oil_service_car_app_order_status_completed',
        methods: ['PUT']
    )]
    public function markCompleted(string $orderId): JsonResponse
    {
        return $this->updateStatus($orderId, OrderStatusEnum::COMPLETED);
    }

    #[OA\Put(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Car App Orders',
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
        '/car-app/orders/{orderId}/status/canceled',
        name: 'oil_service_car_app_order_status_canceled',
        methods: ['PUT']
    )]
    public function markCanceled(string $orderId): JsonResponse
    {
        return $this->updateStatus($orderId, OrderStatusEnum::CANCELED);
    }

    private function updateStatus(string $orderId, OrderStatusEnum $status): JsonResponse
    {
        $user = $this->requireActiveUser();

        $order = $this->orderRepository->find($orderId);

        if ($order === null) {
            throw new NotFoundHttpException();
        }

        $this->orderAccessService->assertUserHasAccessToOrder($order, $user);

        try {
            $order = $this->orderService->updateOrderStatus($order, $status);

            $responseDTO = $this->dtoFactory->createOrderUpdateResponseDTO($order);

            return $this->json($responseDTO);
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
    }

    private function requireActiveUser(): AuthUser
    {
        $user = $this->security->getUser();

        if (!$user instanceof AuthUser) {
            throw new ServerErrorHttpException();
        }

        if (!$user->getIsActive()) {
            throw new AccessDeniedHttpException();
        }

        return $user;
    }
}
