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
use App\Modules\OilService\DTO\CustomerCarCreateRequestDTO;
use App\Modules\OilService\DTO\CustomerCarCreateResponseDTO;
use App\Modules\OilService\DTO\CustomerCarDeleteResponseDTO;
use App\Modules\OilService\DTO\CustomerCarHistoryDeleteResponseDTO;
use App\Modules\OilService\DTO\CustomerCarInfoResponseDTO;
use App\Modules\OilService\DTO\CustomerCarListResponseDTO;
use App\Modules\OilService\DTO\CustomerCarUpdateRequestDTO;
use App\Modules\OilService\DTO\CustomerCarUpdateResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\Modules\OilService\Grid\Enum\CustomerCarGridSortEnum;
use App\OilService\DBAL\Repository\InventoryItemRepository;
use App\CarDatabase\DBAL\Enum\CustomerCarBrandEnum;
use App\CarDatabase\DBAL\Repository\EngineRepository;
use App\OilService\CustomerCarService;
use App\OilService\DBAL\Entity\CustomerCar;
use App\OilService\DBAL\Repository\CustomerCarHistoryRepository;
use App\OilService\DBAL\Repository\CustomerCarRepository;
use App\OilService\DBAL\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

class CustomerCarController extends AbstractController
{
    private const string FILTER_LICENSE_PLATE_KEY = 'licensePlate';
    private const string FILTER_VIN_KEY = 'vin';
    private const string FILTER_BRAND_KEY = 'brand';
    private const string FILTER_MODEL_KEY = 'model';
    private const string FILTER_USER_EMAIL_KEY = 'userEmail';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly CustomerCarRepository $customerCarRepository,
        private readonly CustomerCarHistoryRepository $customerCarHistoryRepository,
        private readonly UserRepository $userRepository,
        private readonly EngineRepository $engineRepository,
        private readonly InventoryItemRepository $inventoryItemRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly CustomerCarService $customerCarService,
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
                    type: CustomerCarCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Customer Cars',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: CustomerCarCreateResponseDTO::class
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
        '/oil-service/customer-cars',
        name: 'oil_service_customer_car_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $createRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                CustomerCarCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($createRequestDTO);

            $user = null;
            if ($createRequestDTO->getUserId() !== null) {
                $user = $this->userRepository->find($createRequestDTO->getUserId());
            }

            $engine = null;
            if ($createRequestDTO->getEngineId() !== null) {
                $engine = $this->engineRepository->find($createRequestDTO->getEngineId());

                if ($engine === null) {
                    throw new BadRequestHttpException('Engine not found.');
                }
            }

            $car = $this->customerCarService->createCustomerCar(
                $createRequestDTO->getLicensePlate(),
                $createRequestDTO->getBrand() !== null ? CustomerCarBrandEnum::from($createRequestDTO->getBrand()) : null,
                $createRequestDTO->getModel(),
                $createRequestDTO->getVin(),
                $user,
                $engine,
            );

            $responseDTO = $this->dtoFactory->createCustomerCarCreateResponseDTO($car);

            return $this->json($responseDTO);
        } catch (ValidationException $exception) {
            return $this->responseFactory->createResponseErrorCollection(
                $exception->getErrorCollection()
            );
        } catch (InvalidDataException) {
            throw new BadRequestHttpException();
        } catch (Throwable $exception) {
            throw new ServerErrorHttpException($exception->getMessage(), $exception);
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
                    type: CustomerCarUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Customer Cars',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: CustomerCarUpdateResponseDTO::class
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
        '/oil-service/customer-cars/{carId}',
        name: 'oil_service_customer_car_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $carId): JsonResponse
    {
        $this->requireAdminUser();

        $car = $this->customerCarRepository->find($carId);

        if ($car === null) {
            throw new NotFoundHttpException();
        }

        try {
            $updateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                CustomerCarUpdateRequestDTO::class
            );
            $updateRequestDTO->setCustomerCarId($carId);

            $this->dtoValueResolver->validateDTO($updateRequestDTO);

            $user = null;
            if ($updateRequestDTO->getUserId() !== null) {
                $user = $this->userRepository->find($updateRequestDTO->getUserId());
            }

            $engine = null;
            if ($updateRequestDTO->getEngineId() !== null) {
                $engine = $this->engineRepository->find($updateRequestDTO->getEngineId());

                if ($engine === null) {
                    throw new BadRequestHttpException('Engine not found.');
                }
            }

            $this->customerCarService->updateCustomerCar(
                $car,
                $updateRequestDTO->getLicensePlate(),
                $updateRequestDTO->getBrand() !== null ? CustomerCarBrandEnum::from($updateRequestDTO->getBrand()) : null,
                $updateRequestDTO->getModel(),
                $updateRequestDTO->getVin(),
                $user,
                $engine,
            );

            $responseDTO = $this->dtoFactory->createCustomerCarUpdateResponseDTO($car);

            return $this->json($responseDTO);
        } catch (ValidationException $exception) {
            return $this->responseFactory->createResponseErrorCollection(
                $exception->getErrorCollection()
            );
        } catch (InvalidDataException) {
            throw new BadRequestHttpException();
        } catch (Throwable $exception) {
            throw new ServerErrorHttpException($exception->getMessage(), $exception);
        }
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Customer Cars',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: CustomerCarInfoResponseDTO::class
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
        '/oil-service/customer-cars/{carId}',
        name: 'oil_service_customer_car_info',
        methods: ['GET']
    )]
    public function info(string $carId): JsonResponse
    {
        $this->requireAdminUser();

        $car = $this->customerCarRepository->find($carId);

        if ($car === null) {
            throw new NotFoundHttpException();
        }

        $engineFilters = [];
        $engine = $car->getEngine();

        if ($engine !== null) {
            foreach ($engine->getEngineFilters()->toArray() as $engineFilter) {
                $oemCode = $engineFilter->getFilter()->getOemCode();
                $inventoryItem = null;

                if (is_string($oemCode) && trim($oemCode) !== '') {
                    $inventoryItem = $this->inventoryItemRepository->findInStockByOemCode($oemCode);
                }

                $engineFilters[] = $this->dtoFactory->createCustomerCarEngineFilterDTO(
                    $engineFilter,
                    $inventoryItem,
                );
            }
        }

        $responseDTO = $this->dtoFactory->createCustomerCarInfoResponseDTO($car, $engineFilters);

        return $this->json($responseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Customer Cars',
        ],
        parameters: [
            new OA\Parameter(
                name: self::FILTER_LICENSE_PLATE_KEY,
                description: 'Filter by license plate (contains)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_VIN_KEY,
                description: 'Filter by VIN (contains)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_BRAND_KEY,
                description: 'Filter by brand',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: CustomerCarBrandEnum::VALUES),
                example: 'skoda'
            ),
            new OA\Parameter(
                name: self::FILTER_MODEL_KEY,
                description: 'Filter by model (contains)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: self::FILTER_USER_EMAIL_KEY,
                description: 'Filter by customer email (contains)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
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
                description: 'Number of items on the page, default value '
                    . ApiGridPropertyHelper::MAX_RESULTS_DEFAULT_VALUE,
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: ApiGridPropertyHelper::MAX_RESULTS_DEFAULT_VALUE
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::SORT_KEY,
                description: 'Sorting by values, default value licensePlate',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'licensePlate'
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
                content: new Model(
                    type: CustomerCarListResponseDTO::class
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/oil-service/customer-cars',
        name: 'oil_service_customer_car_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            try {
                $licensePlate = $request->query->get(self::FILTER_LICENSE_PLATE_KEY);

                assert(is_string($licensePlate));

                $qb->andWhere(
                    $qb->expr()->like(
                        CustomerCarRepository::ALIAS . '.licensePlate',
                        ':licensePlate'
                    )
                );
                $qb->setParameter('licensePlate', '%' . $licensePlate . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $vin = $request->query->get(self::FILTER_VIN_KEY);

                assert(is_string($vin));

                $qb->andWhere(
                    $qb->expr()->like(
                        CustomerCarRepository::ALIAS . '.vin',
                        ':vin'
                    )
                );
                $qb->setParameter('vin', '%' . $vin . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $brand = $request->query->get(self::FILTER_BRAND_KEY);

                assert(is_string($brand));

                $brandEnum = CustomerCarBrandEnum::tryFrom($brand);

                if ($brandEnum !== null) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            CustomerCarRepository::ALIAS . '.brand',
                            ':brand'
                        )
                    );
                    $qb->setParameter('brand', $brandEnum->value);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $model = $request->query->get(self::FILTER_MODEL_KEY);

                assert(is_string($model));

                $qb->andWhere(
                    $qb->expr()->like(
                        CustomerCarRepository::ALIAS . '.model',
                        ':model'
                    )
                );
                $qb->setParameter('model', '%' . $model . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $email = $request->query->get(self::FILTER_USER_EMAIL_KEY);

                assert(is_string($email));

                $qb->leftJoin(CustomerCarRepository::ALIAS . '.user', 'osu');
                $qb->andWhere(
                    $qb->expr()->like('osu.email', ':userEmail')
                );
                $qb->setParameter('userEmail', '%' . $email . '%');
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::ASC);
        $sortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            CustomerCarGridSortEnum::class,
            CustomerCarGridSortEnum::LICENSE_PLATE
        );
        $carsQueryBuilder = $this->customerCarRepository->getQueryBuilderWithAlias();
        $carsPaginator = $this->apiGridManager->createPaginator(
            $carsQueryBuilder,
            $queryModifier
        );

        /** @var CustomerCar[] $cars */
        $cars = $this->apiGridManager->fetchData(
            $carsQueryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );

        $responseDTO = $this->dtoFactory->createCustomerCarListResponseDTO(
            $cars,
            $this->apiGridPropertyHelper->createPageCount(
                $carsPaginator->count(),
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
            'Customer Cars',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: CustomerCarDeleteResponseDTO::class
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
        '/oil-service/customer-cars/{carId}',
        name: 'oil_service_customer_car_delete',
        methods: ['DELETE']
    )]
    public function delete(string $carId): JsonResponse
    {
        $this->requireAdminUser();

        $car = $this->customerCarRepository->find($carId);

        if ($car === null) {
            throw new NotFoundHttpException();
        }

        try {
            $this->customerCarService->deleteCustomerCar($car);

            $responseDTO = $this->dtoFactory->createCustomerCarDeleteResponseDTO();

            return $this->json($responseDTO);
        } catch (ValidationException $exception) {
            return $this->responseFactory->createResponseErrorCollection(
                $exception->getErrorCollection()
            );
        } catch (Throwable $exception) {
            throw new ServerErrorHttpException($exception->getMessage(), $exception);
        }
    }

    #[OA\Put(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Customer Cars',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: CustomerCarUpdateResponseDTO::class
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
        '/oil-service/customer-cars/{carId}/data-cube',
        name: 'oil_service_customer_car_update_data_cube',
        methods: ['PUT']
    )]
    public function updateDataCube(string $carId): JsonResponse
    {
        $this->requireAdminUser();

        $car = $this->customerCarRepository->find($carId);

        if ($car === null) {
            throw new NotFoundHttpException();
        }

        $vin = $car->getVin();

        if ($vin === null || $vin === '') {
            throw new BadRequestHttpException('VIN is missing.');
        }

        try {
            $success = $this->customerCarService->updateFromDataCube($car, $vin);

            if (!$success) {
                throw new BadRequestHttpException('Vehicle data not found in Data Cube.');
            }

            $responseDTO = $this->dtoFactory->createCustomerCarUpdateResponseDTO($car);

            return $this->json($responseDTO);
        } catch (InvalidDataException) {
            throw new BadRequestHttpException();
        } catch (Throwable $exception) {
            throw new ServerErrorHttpException($exception->getMessage(), $exception);
        }
    }

    #[OA\Put(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Customer Cars',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: CustomerCarUpdateResponseDTO::class
                )
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
        '/oil-service/customer-cars/{carId}/engine/resolve',
        name: 'oil_service_customer_car_resolve_engine',
        methods: ['PUT']
    )]
    public function resolveEngine(string $carId): JsonResponse
    {
        $this->requireAdminUser();

        $car = $this->customerCarRepository->find($carId);

        if ($car === null) {
            throw new NotFoundHttpException();
        }

        $engineCode = $car->getDkMotorTyp();

        if ($engineCode === null || trim($engineCode) === '') {
            throw new BadRequestHttpException('Engine code is missing.');
        }

        $engine = $this->customerCarService->resolveEngineByCode($car, $engineCode);

        if ($engine === null) {
            throw new BadRequestHttpException('Engine not found.');
        }

        $responseDTO = $this->dtoFactory->createCustomerCarUpdateResponseDTO($car);

        return $this->json($responseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Customer Cars',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: CustomerCarHistoryDeleteResponseDTO::class
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
        '/oil-service/customer-cars/history/{historyId}',
        name: 'oil_service_customer_car_history_delete',
        methods: ['DELETE']
    )]
    public function deleteHistory(string $historyId): JsonResponse
    {
        $this->requireAdminUser();

        $history = $this->customerCarHistoryRepository->find($historyId);

        if ($history === null) {
            throw new NotFoundHttpException();
        }

        $this->entityManager->remove($history);
        $this->entityManager->flush();

        $responseDTO = $this->dtoFactory->createCustomerCarHistoryDeleteResponseDTO();

        return $this->json($responseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Customer Cars',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: CustomerCarHistoryDeleteResponseDTO::class
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
        '/oil-service/customer-cars/{carId}/history',
        name: 'oil_service_customer_car_history_delete_all',
        methods: ['DELETE']
    )]
    public function deleteAllHistory(string $carId): JsonResponse
    {
        $this->requireAdminUser();

        $car = $this->customerCarRepository->find($carId);

        if ($car === null) {
            throw new NotFoundHttpException();
        }

        $this->customerCarService->deleteCustomerCarHistory($car);

        $responseDTO = $this->dtoFactory->createCustomerCarHistoryDeleteResponseDTO();

        return $this->json($responseDTO);
    }

    private function requireAdminUser(): AuthUser
    {
        $user = $this->security->getUser();

        if (!$user instanceof AuthUser) {
            throw new AccessDeniedHttpException();
        }

        return $user;
    }
}
