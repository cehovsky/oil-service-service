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
use App\Modules\OilService\DTO\FormCreateWithTermRequestDTO;
use App\Modules\OilService\DTO\FormDeleteResponseDTO;
use App\Modules\OilService\DTO\FormInfoResponseDTO;
use App\Modules\OilService\DTO\FormListResponseDTO;
use App\Modules\OilService\DTO\FormUpdateRequestDTO;
use App\Modules\OilService\DTO\FormUpdateResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\Modules\OilService\Grid\Enum\FormGridSortEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Enum\FormStatusEnum;
use App\OilService\DBAL\Entity\Form;
use App\OilService\DBAL\Entity\Route as RouteEntity;
use App\OilService\DBAL\Repository\FormRepository;
use App\OilService\DBAL\Repository\RouteRepository;
use App\OilService\FormService;
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

class FormController extends AbstractController
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
        private readonly FormRepository $formRepository,
        private readonly RouteRepository $routeRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly FormService $formService,
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
                    type: FormCreateWithTermRequestDTO::class
                ),
            )
        ),
        tags: [
            'OilService',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: FormInfoResponseDTO::class
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
        '/oil-service/forms',
        name: 'oil_service_form_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $formCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                FormCreateWithTermRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($formCreateRequestDTO);

            $route = $this->findRoute($formCreateRequestDTO->getRouteId());

            $form = $this->formService->createFormWithUser(
                $formCreateRequestDTO->getFullName(),
                $formCreateRequestDTO->getPhone(),
                $formCreateRequestDTO->getEmail(),
                $formCreateRequestDTO->getCarModel(),
                $formCreateRequestDTO->getLicensePlate(),
                $formCreateRequestDTO->getAddress(),
                $formCreateRequestDTO->getNote(),
                $formCreateRequestDTO->getIsCompany(),
                $formCreateRequestDTO->getCompanyName(),
                $formCreateRequestDTO->getCompanyIdentificationNumber(),
                $formCreateRequestDTO->getCompanyTaxId(),
                $formCreateRequestDTO->getCompanyAddress(),
                FormStatusEnum::from($formCreateRequestDTO->getStatus()),
                RealizationTimeSlotEnum::from($formCreateRequestDTO->getRealizationTimeSlot()),
                $this->createRealizationDate($formCreateRequestDTO->getRealizationDate()),
                $route,
            );

            $formInfoResponseDTO = $this->dtoFactory->createFormInfoResponseDTO($form);

            return $this->json($formInfoResponseDTO);
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
                    type: FormUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'OilService',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: FormUpdateResponseDTO::class
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
        '/oil-service/forms/{formId}',
        name: 'oil_service_form_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $formId): JsonResponse
    {
        $this->requireAdminUser();

        $form = $this->formRepository->find($formId);

        if ($form === null) {
            throw new NotFoundHttpException();
        }

        try {
            $formUpdateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                FormUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($formUpdateRequestDTO);

            $routeProvided = $this->isFieldProvided($request, 'routeId');

            $form = $this->formService->updateForm(
                $form,
                $formUpdateRequestDTO->getFullName(),
                $formUpdateRequestDTO->getPhone(),
                $formUpdateRequestDTO->getEmail(),
                $formUpdateRequestDTO->getCarModel(),
                $formUpdateRequestDTO->getLicensePlate(),
                $formUpdateRequestDTO->getAddress(),
                $formUpdateRequestDTO->getNote(),
                FormStatusEnum::from($formUpdateRequestDTO->getStatus()),
                RealizationTimeSlotEnum::from($formUpdateRequestDTO->getRealizationTimeSlot()),
                $this->formService->createRealizationDate($formUpdateRequestDTO->getRealizationDate()),
                $formUpdateRequestDTO->getIsCompany(),
                $formUpdateRequestDTO->getCompanyName(),
                $formUpdateRequestDTO->getCompanyIdentificationNumber(),
                $formUpdateRequestDTO->getCompanyTaxId(),
                $formUpdateRequestDTO->getCompanyAddress(),
                $formUpdateRequestDTO->getUserEmail(),
                $routeProvided,
                $formUpdateRequestDTO->getRouteId(),
            );

            $formUpdateResponseDTO = $this->dtoFactory->createFormUpdateResponseDTO($form);

            return $this->json($formUpdateResponseDTO);
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
            'OilService',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: FormInfoResponseDTO::class
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
        '/oil-service/forms/{formId}',
        name: 'oil_service_form_info',
        methods: ['GET']
    )]
    public function info(string $formId): JsonResponse
    {
        $this->requireAdminUser();

        $form = $this->formRepository->find($formId);

        if ($form === null) {
            throw new NotFoundHttpException();
        }

        $formInfoResponseDTO = $this->dtoFactory->createFormInfoResponseDTO($form);

        return $this->json($formInfoResponseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'OilService',
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
                    enum: FormStatusEnum::VALUES
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
                    type: FormListResponseDTO::class
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/oil-service/forms',
        name: 'oil_service_form_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            // Join user for filtering
            $qb->leftJoin(FormRepository::ALIAS . '.user', 'u');

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
                            FormRepository::ALIAS . '.ident',
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
                        FormRepository::ALIAS . '.fullName',
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
                        FormRepository::ALIAS . '.email',
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
                        FormRepository::ALIAS . '.phone',
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
                        FormRepository::ALIAS . '.carModel',
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
                        FormRepository::ALIAS . '.licensePlate',
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
                        FormRepository::ALIAS . '.isCompany',
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

                $statusEnum = FormStatusEnum::tryFrom($status);

                if ($statusEnum !== null) {
                    $qb->andWhere(
                        $qb->expr()->eq(
                            FormRepository::ALIAS . '.status',
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
                            FormRepository::ALIAS . '.realizationTimeSlot',
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
                            FormRepository::ALIAS . '.realizationDate',
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
        $formGridSortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            FormGridSortEnum::class,
            FormGridSortEnum::CREATED_AT
        );
        $formsQueryBuilder = $this->formRepository->getQueryBuilderWithAlias();
        $formsPaginator = $this->apiGridManager->createPaginator(
            $formsQueryBuilder,
            $queryModifier
        );
        /** @var Form[] $forms */
        $forms = $this->apiGridManager->fetchData(
            $formsQueryBuilder,
            $formGridSortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );
        $formListResponseDTO = $this->dtoFactory->createFormListResponseDTO(
            $forms,
            $this->apiGridPropertyHelper->createPageCount(
                $formsPaginator->count(),
                $maxResults
            )
        );

        return $this->json($formListResponseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'OilService',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: FormDeleteResponseDTO::class
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
        '/oil-service/forms/{formId}',
        name: 'oil_service_form_delete',
        methods: ['DELETE']
    )]
    public function delete(string $formId): JsonResponse
    {
        $this->requireAdminUser();

        $form = $this->formRepository->find($formId);

        if ($form === null) {
            throw new NotFoundHttpException();
        }

        $this->formService->deleteForm($form);

        $formDeleteResponseDTO = $this->dtoFactory->createFormDeleteResponseDTO();

        return $this->json($formDeleteResponseDTO);
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
