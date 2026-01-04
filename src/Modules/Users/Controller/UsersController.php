<?php

declare(strict_types=1);

namespace App\Modules\Users\Controller;

use App\Auth\DBAL\Entity\User;
use App\Auth\DBAL\Repository\UserRepository;
use App\Auth\Factory\EntityFactory;
use App\Domain\ApiGrid\ApiGridManager;
use App\Domain\ApiGrid\ApiGridPropertyHelper;
use App\Domain\ApiGrid\OrderEnum;
use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Modules\Users\DTO\UsersCreateRequestDTO;
use App\Modules\Users\DTO\UsersCreateResponseDTO;
use App\Modules\Users\DTO\UsersDeleteResponseDTO;
use App\Modules\Users\DTO\UsersInfoResponseDTO;
use App\Modules\Users\DTO\UsersListResponseDTO;
use App\Modules\Users\DTO\UsersUpdateRequestDTO;
use App\Modules\Users\DTO\UsersUpdateResponseDTO;
use App\Modules\Users\Factory\DTOFactory;
use App\Modules\Users\Grid\Enum\UsersGridSortEnum;
use App\OilService\DBAL\Entity\RouteUser;
use App\Modules\Users\Validation\Constraint\UniqueUserEmail;
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
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class UsersController extends AbstractController
{
    private const string FILTER_EMAIL_KEY = 'email';
    private const string FILTER_FULL_NAME_KEY = 'fullName';
    private const string FILTER_IS_ACTIVE_KEY = 'isActive';
    private const string FILTER_IS_ADMIN_KEY = 'isAdmin';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly PasswordHasherFactoryInterface $passwordHasherFactory,
        private readonly EntityFactory $entityFactory,
        private readonly Security $security,
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
                    type: UsersCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Users',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Created',
                content: new Model(
                    type: UsersCreateResponseDTO::class
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
        '/users/users',
        name: 'users_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $usersCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                UsersCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO(
                $usersCreateRequestDTO,
                new UniqueUserEmail(),
            );

            $user = $this->entityFactory->createUser(
                $usersCreateRequestDTO->getEmail(),
                $this->hashPassword($usersCreateRequestDTO->getPassword()),
                $usersCreateRequestDTO->getFullName(),
                $usersCreateRequestDTO->getIsActive(),
                $usersCreateRequestDTO->getIsAdmin(),
            );

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $usersCreateResponseDTO = $this->dtoFactory->createUsersCreateResponseDTO($user);

            return $this->json($usersCreateResponseDTO);
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
                    type: UsersUpdateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Users',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new Model(
                    type: UsersUpdateResponseDTO::class
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
        '/users/users/{userId}',
        name: 'users_update',
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
            $usersUpdateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                UsersUpdateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO(
                $usersUpdateRequestDTO,
                new UniqueUserEmail($user->getId()->__toString()),
            );

            $user->setEmail($usersUpdateRequestDTO->getEmail());
            $user->setFullName($usersUpdateRequestDTO->getFullName());
            $user->setIsActive($usersUpdateRequestDTO->getIsActive());
            $user->setIsAdmin($usersUpdateRequestDTO->getIsAdmin());

            $password = $usersUpdateRequestDTO->getPassword();

            if ($password !== null && $password !== '') {
                $user->setPassword($this->hashPassword($password));
            }

            $this->entityManager->flush();

            $usersUpdateResponseDTO = $this->dtoFactory->createUsersUpdateResponseDTO($user);

            return $this->json($usersUpdateResponseDTO);
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
            'Users',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: UsersInfoResponseDTO::class
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
        '/users/users/{userId}',
        name: 'users_info',
        methods: ['GET']
    )]
    public function info(string $userId): JsonResponse
    {
        $this->requireAdminUser();

        $user = $this->userRepository->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        $usersInfoResponseDTO = $this->dtoFactory->createUsersInfoResponseDTO($user);

        return $this->json($usersInfoResponseDTO);
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Users',
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
                example: 'admin@example.com'
            ),
            new OA\Parameter(
                name: self::FILTER_FULL_NAME_KEY,
                description: 'non-strict filtering',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'string'
                ),
                example: 'Admin User'
            ),
            new OA\Parameter(
                name: self::FILTER_IS_ACTIVE_KEY,
                description: 'strict filtered',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'boolean'
                ),
                example: true
            ),
            new OA\Parameter(
                name: self::FILTER_IS_ADMIN_KEY,
                description: 'strict filtered',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'boolean'
                ),
                example: false
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
                    type: UsersListResponseDTO::class
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/users/users',
        name: 'users_list',
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
                $isActiveRaw = $request->query->get(self::FILTER_IS_ACTIVE_KEY);

                assert($isActiveRaw !== null);

                $isActive = filter_var($isActiveRaw, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

                assert($isActive !== null);

                $qb->andWhere(
                    $qb->expr()->eq(
                        UserRepository::ALIAS . '.isActive',
                        ':isActive'
                    )
                );
                $qb->setParameter('isActive', $isActive);
            } catch (Throwable) {
                // pass
            }

            try {
                $isAdminRaw = $request->query->get(self::FILTER_IS_ADMIN_KEY);

                assert($isAdminRaw !== null);

                $isAdmin = filter_var($isAdminRaw, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

                assert($isAdmin !== null);

                $qb->andWhere(
                    $qb->expr()->eq(
                        UserRepository::ALIAS . '.isAdmin',
                        ':isAdmin'
                    )
                );
                $qb->setParameter('isAdmin', $isAdmin);
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::ASC);
        $usersGridSortEnum = $this->apiGridPropertyHelper->createSortEnum(
            $request,
            UsersGridSortEnum::class,
            UsersGridSortEnum::EMAIL
        );
        $usersQueryBuilder = $this->userRepository->getQueryBuilderWithAlias();
        $usersPaginator = $this->apiGridManager->createPaginator(
            $usersQueryBuilder,
            $queryModifier
        );
        /** @var User[] $users */
        $users = $this->apiGridManager->fetchData(
            $usersQueryBuilder,
            $usersGridSortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );
        $usersListResponseDTO = $this->dtoFactory->createUsersListResponseDTO(
            $users,
            $this->apiGridPropertyHelper->createPageCount(
                $usersPaginator->count(),
                $maxResults
            )
        );

        return $this->json($usersListResponseDTO);
    }

    #[OA\Delete(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Users',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Deleted',
                content: new Model(
                    type: UsersDeleteResponseDTO::class
                )
            ),
            new OA\Response(
                response: 400,
                description: 'User assigned to a route'
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
        '/users/users/{userId}',
        name: 'users_delete',
        methods: ['DELETE']
    )]
    public function delete(string $userId): JsonResponse
    {
        $this->requireAdminUser();

        $user = $this->userRepository->find($userId);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        $assignedRouteUsersCount = (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(routeUser.id)')
            ->from(RouteUser::class, 'routeUser')
            ->andWhere('routeUser.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        if ($assignedRouteUsersCount > 0) {
            throw new BadRequestHttpException('User assigned to a route cannot be deleted.');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $usersDeleteResponseDTO = $this->dtoFactory->createUsersDeleteResponseDTO();

        return $this->json($usersDeleteResponseDTO);
    }

    private function requireAdminUser(): User
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ServerErrorHttpException();
        }

        if (!$user->getIsAdmin()) {
            throw new AccessDeniedHttpException();
        }

        return $user;
    }

    private function hashPassword(string $plainPassword): string
    {
        return $this->passwordHasherFactory
            ->getPasswordHasher(User::class)
            ->hash($plainPassword);
    }
}
