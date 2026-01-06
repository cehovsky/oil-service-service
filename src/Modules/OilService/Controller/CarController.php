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
use App\Modules\OilService\DTO\CarCreateRequestDTO;
use App\Modules\OilService\DTO\CarCreateResponseDTO;
use App\Modules\OilService\DTO\CarDeleteResponseDTO;
use App\Modules\OilService\DTO\CarInfoResponseDTO;
use App\Modules\OilService\DTO\CarListResponseDTO;
use App\Modules\OilService\DTO\CarUpdateRequestDTO;
use App\Modules\OilService\DTO\CarUpdateResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\Modules\OilService\Grid\Enum\CarGridSortEnum;
use App\OilService\DBAL\Entity\Car;
use App\OilService\DBAL\Enum\CarStatusEnum;
use App\OilService\DBAL\Repository\CarRepository;
use App\OilService\CarService;
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

class CarController extends AbstractController
{
    private const string FILTER_LABEL_KEY = 'label';
    private const string FILTER_IDENT_KEY = 'ident';
    private const string FILTER_LICENSE_PLATE_KEY = 'licensePlate';
    private const string FILTER_STATUS_KEY = 'status';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly CarRepository $carRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly CarService $carService,
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
                    type: CarCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Cars',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: CarCreateResponseDTO::class
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
        '/oil-service/cars',
        name: 'oil_service_car_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $carCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                CarCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($carCreateRequestDTO);

            $car = $this->carService->createCar(
                $carCreateRequestDTO->getLabel(),
                $carCreateRequestDTO->getIdent(),
                $carCreateRequestDTO->getLicensePlate(),
                CarStatusEnum::from($carCreateRequestDTO->getStatus()),
            );

            $carCreateResponseDTO = $this->dtoFactory->createCarCreateResponseDTO($car);

            return $this->json($carCreateResponseDTO);
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
                    type: CarUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Cars',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: CarUpdateResponseDTO::class
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
        '/oil-service/cars/{carId}',
        name: 'oil_service_car_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $carId): JsonResponse
    {
        $this->requireAdminUser();

        $car = $this->carRepository->find($carId);

        if ($car === null) {
            throw new NotFoundHttpException();
        }

        try {
            $carUpdateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                CarUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($carUpdateRequestDTO);

            $this->carService->updateCar(
                $car,
                $carUpdateRequestDTO->getLabel(),
                $carUpdateRequestDTO->getIdent(),
                $carUpdateRequestDTO->getLicensePlate(),
                CarStatusEnum::from($carUpdateRequestDTO->getStatus()),
            );

            $carUpdateResponseDTO = $this->dtoFactory->createCarUpdateResponseDTO($car);

            return $this->json($carUpdateResponseDTO);
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
            'Cars',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: CarInfoResponseDTO::class
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
        '/oil-service/cars/{carId}',
        name: 'oil_service_car_info',
        methods: ['GET']
    )]
    public function info(string $carId): JsonResponse
    {
        $this->requireAdminUser();

        $car = $this->carRepository->find($carId);

        if ($car === null) {
            throw new NotFoundHttpException();
        }

        $carInfoResponseDTO = $this->dtoFactory->createCarInfoResponseDTO($car);

        return $this->json($carInfoResponseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Cars',
        ],
        parameters: [
            new OA\Parameter(
                name: self::FILTER_LABEL_KEY,
                description: 'Filter by label (non-strict)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'Ford'
            ),
            new OA\Parameter(
                name: self::FILTER_IDENT_KEY,
                description: 'Filter by ident (strict)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'CAR001'
            ),
            new OA\Parameter(
                name: self::FILTER_LICENSE_PLATE_KEY,
                description: 'Filter by license plate (non-strict)',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: '1A2'
            ),
            new OA\Parameter(
                name: self::FILTER_STATUS_KEY,
                description: 'Filter by status',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string',
                    enum: CarStatusEnum::VALUES
                ),
                example: 'operational'
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
                description: 'Sorting by values, default value label',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'label'
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
                    type: CarListResponseDTO::class
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/oil-service/cars',
        name: 'oil_service_car_list',
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
                        CarRepository::ALIAS . '.label',
                        ':label'
                    )
                );
                $qb->setParameter('label', '%' . $label . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $ident = $request->query->get(self::FILTER_IDENT_KEY);

                assert(is_string($ident));

                $qb->andWhere(
                    $qb->expr()->eq(
                        CarRepository::ALIAS . '.ident',
                        ':ident'
                    )
                );
                $qb->setParameter('ident', $ident);
            } catch (Throwable) {
                // pass
            }

            try {
                $licensePlate = $request->query->get(self::FILTER_LICENSE_PLATE_KEY);

                assert(is_string($licensePlate));

                $qb->andWhere(
                    $qb->expr()->like(
                        CarRepository::ALIAS . '.licensePlate',
                        ':licensePlate'
                    )
                );
                $qb->setParameter('licensePlate', '%' . $licensePlate . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $status = $request->query->get(self::FILTER_STATUS_KEY);

                assert(is_string($status));

                $statusEnum = CarStatusEnum::tryFrom($status);

                if ($statusEnum !== null) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            CarRepository::ALIAS . '.status',
                            ':status'
                        )
                    );
                    $qb->setParameter('status', $statusEnum->value);
                }
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::ASC);
        $carGridSortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            CarGridSortEnum::class,
            CarGridSortEnum::LABEL
        );
        $carsQueryBuilder = $this->carRepository->getQueryBuilderWithAlias();
        $carsPaginator = $this->apiGridManager->createPaginator(
            $carsQueryBuilder,
            $queryModifier
        );
        /** @var Car[] $cars */
        $cars = $this->apiGridManager->fetchData(
            $carsQueryBuilder,
            $carGridSortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );
        $carListResponseDTO = $this->dtoFactory->createCarListResponseDTO(
            $cars,
            $this->apiGridPropertyHelper->createPageCount(
                $carsPaginator->count(),
                $maxResults
            )
        );

        return $this->json($carListResponseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Cars',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: CarDeleteResponseDTO::class
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
        '/oil-service/cars/{carId}',
        name: 'oil_service_car_delete',
        methods: ['DELETE']
    )]
    public function delete(string $carId): JsonResponse
    {
        $this->requireAdminUser();

        $car = $this->carRepository->find($carId);

        if ($car === null) {
            throw new NotFoundHttpException();
        }

        try {
            $this->carService->deleteCar($car);
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection($e->getErrorCollection());
        }

        $carDeleteResponseDTO = $this->dtoFactory->createCarDeleteResponseDTO();

        return $this->json($carDeleteResponseDTO);
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
