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
use App\Modules\OilService\DTO\OilServiceUserCreateRequestDTO;
use App\Modules\OilService\DTO\OilServiceUserCreateResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserDeleteResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserInfoResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserListResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserUpdateRequestDTO;
use App\Modules\OilService\DTO\OilServiceUserUpdateResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\Modules\OilService\Grid\Enum\OilServiceUserGridSortEnum;
use App\Modules\OilService\Validation\Constraint\UniqueOilServiceUserEmail;
use App\OilService\DBAL\Repository\UserRepository;
use App\OilService\DBAL\Entity\User;
use App\OilService\OilServiceUserService;
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

class OilServiceUserController extends AbstractController
{
    private const string FILTER_EMAIL_KEY = 'email';
    private const string FILTER_FULL_NAME_KEY = 'fullName';
    private const string FILTER_PHONE_KEY = 'phone';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly UserRepository $userRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly OilServiceUserService $oilServiceUserService,
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
                    type: OilServiceUserCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Customers',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: OilServiceUserCreateResponseDTO::class
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
        '/oil-service/users',
        name: 'oil_service_user_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $userCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                OilServiceUserCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO(
                $userCreateRequestDTO,
                new UniqueOilServiceUserEmail(),
            );

            $user = $this->oilServiceUserService->createUser(
                $userCreateRequestDTO->getEmail(),
                $userCreateRequestDTO->getPhone(),
                $userCreateRequestDTO->getFullName(),
            );

            $userCreateResponseDTO = $this->dtoFactory->createOilServiceUserCreateResponseDTO($user);

            return $this->json($userCreateResponseDTO);
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
                    type: OilServiceUserUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Customers',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: OilServiceUserUpdateResponseDTO::class
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
        '/oil-service/users/{userId}',
        name: 'oil_service_user_update',
        methods: ['PUT']
    )]
    public function update(Request $request, string $userId): JsonResponse
    {
        $this->requireAdminUser();

        $user = $this->userRepository->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        try {
            $userUpdateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                OilServiceUserUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO(
                $userUpdateRequestDTO,
                new UniqueOilServiceUserEmail($user->getId()->__toString()),
            );

            $user = $this->oilServiceUserService->updateUser(
                $user,
                $userUpdateRequestDTO->getEmail(),
                $userUpdateRequestDTO->getPhone(),
                $userUpdateRequestDTO->getFullName(),
            );

            $userUpdateResponseDTO = $this->dtoFactory->createOilServiceUserUpdateResponseDTO($user);

            return $this->json($userUpdateResponseDTO);
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
            'Customers',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: OilServiceUserInfoResponseDTO::class
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
        '/oil-service/users/{userId}',
        name: 'oil_service_user_info',
        methods: ['GET']
    )]
    public function info(string $userId): JsonResponse
    {
        $this->requireAdminUser();

        $user = $this->userRepository->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        $userInfoResponseDTO = $this->dtoFactory->createOilServiceUserInfoResponseDTO($user);

        return $this->json($userInfoResponseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Customers',
        ],
        parameters: [
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
                description: 'Sorting by values, default value email',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'email'
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
                    type: OilServiceUserListResponseDTO::class
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/oil-service/users',
        name: 'oil_service_user_list',
        methods: ['GET']
    )]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            try {
                $email = $request->query->get(self::FILTER_EMAIL_KEY);

                assert(is_string($email));

                $qb->andWhere(
                    $qb->expr()->like(
                        UserRepository::ALIAS . '.email',
                        ':email'
                    )
                );
                $qb->setParameter('email', '%' . $email . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $fullName = $request->query->get(self::FILTER_FULL_NAME_KEY);

                assert(is_string($fullName));

                $qb->andWhere(
                    $qb->expr()->like(
                        UserRepository::ALIAS . '.fullName',
                        ':fullName'
                    )
                );
                $qb->setParameter('fullName', '%' . $fullName . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $phone = $request->query->get(self::FILTER_PHONE_KEY);

                assert(is_string($phone));

                $qb->andWhere(
                    $qb->expr()->like(
                        UserRepository::ALIAS . '.phone',
                        ':phone'
                    )
                );
                $qb->setParameter('phone', '%' . $phone . '%');
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::ASC);
        $userGridSortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            OilServiceUserGridSortEnum::class,
            OilServiceUserGridSortEnum::EMAIL
        );
        $usersQueryBuilder = $this->userRepository->getQueryBuilderWithAlias();
        $usersPaginator = $this->apiGridManager->createPaginator(
            $usersQueryBuilder,
            $queryModifier
        );
        /** @var User[] $users */
        $users = $this->apiGridManager->fetchData(
            $usersQueryBuilder,
            $userGridSortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );
        $userListResponseDTO = $this->dtoFactory->createOilServiceUserListResponseDTO(
            $users,
            $this->apiGridPropertyHelper->createPageCount(
                $usersPaginator->count(),
                $maxResults
            )
        );

        return $this->json($userListResponseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Customers',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: OilServiceUserDeleteResponseDTO::class
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
        '/oil-service/users/{userId}',
        name: 'oil_service_user_delete',
        methods: ['DELETE']
    )]
    public function delete(string $userId): JsonResponse
    {
        $this->requireAdminUser();

        $user = $this->userRepository->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        $this->oilServiceUserService->deleteUser($user);

        $userDeleteResponseDTO = $this->dtoFactory->createOilServiceUserDeleteResponseDTO();

        return $this->json($userDeleteResponseDTO);
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
