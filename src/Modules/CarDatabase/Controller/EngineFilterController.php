<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\CarDatabase\DBAL\Repository\EngineFilterRepository;
use App\CarDatabase\DBAL\Repository\EngineRepository;
use App\CarDatabase\DBAL\Repository\FilterRepository;
use App\CarDatabase\EngineFilterService;
use App\Domain\ApiGrid\ApiGridManager;
use App\Domain\ApiGrid\ApiGridPropertyHelper;
use App\Domain\ApiGrid\OrderEnum;
use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Modules\CarDatabase\DTO\EngineFilterCreateRequestDTO;
use App\Modules\CarDatabase\DTO\EngineFilterCreateResponseDTO;
use App\Modules\CarDatabase\DTO\EngineFilterDeleteResponseDTO;
use App\Modules\CarDatabase\DTO\EngineFilterInfoResponseDTO;
use App\Modules\CarDatabase\DTO\EngineFilterListResponseDTO;
use App\Modules\CarDatabase\DTO\EngineFilterUpdateRequestDTO;
use App\Modules\CarDatabase\DTO\EngineFilterUpdateResponseDTO;
use App\Modules\CarDatabase\Factory\DTOFactory;
use App\Modules\CarDatabase\Grid\Enum\EngineFilterGridSortEnum;
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

class EngineFilterController extends AbstractController
{
    private const string FILTER_ENGINE_ID_KEY = 'engineId';
    private const string FILTER_FILTER_ID_KEY = 'filterId';
    private const string FILTER_IS_PRIMARY_KEY = 'isPrimary';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly EngineFilterRepository $engineFilterRepository,
        private readonly EngineRepository $engineRepository,
        private readonly FilterRepository $filterRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly EngineFilterService $engineFilterService,
    ) {
    }

    #[OA\Post(
        security: [[
            'Bearer' => [],
        ]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: new Model(type: EngineFilterCreateRequestDTO::class))
        ),
        tags: ['Car Database Engine Filters'],
        responses: [
            new OA\Response(response: 200, description: 'Created', content: new Model(type: EngineFilterCreateResponseDTO::class)),
            new OA\Response(response: 400, description: 'Bad request', content: new Model(type: ErrorCollection::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/engine-filters', name: 'car_database_engine_filter_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $createRequestDTO = $this->dtoValueResolver->resolveRequest($request, EngineFilterCreateRequestDTO::class);
            $this->dtoValueResolver->validateDTO($createRequestDTO);

            $engine = $this->engineRepository->find($createRequestDTO->getEngineId());
            $filter = $this->filterRepository->find($createRequestDTO->getFilterId());

            if ($engine === null || $filter === null) {
                throw new BadRequestHttpException('Engine or filter not found.');
            }

            $engineFilter = $this->engineFilterService->createEngineFilter(
                $engine,
                $filter,
                $createRequestDTO->isPrimary(),
                $createRequestDTO->getSource(),
            );

            return $this->json($this->dtoFactory->createEngineFilterCreateResponseDTO($engineFilter));
        } catch (ValidationException $exception) {
            return $this->responseFactory->createResponseErrorCollection($exception->getErrorCollection());
        } catch (InvalidDataException) {
            throw new BadRequestHttpException();
        } catch (Throwable $exception) {
            throw new ServerErrorHttpException($exception->getMessage(), $exception);
        }
    }

    #[OA\Put(
        security: [[
            'Bearer' => [],
        ]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: new Model(type: EngineFilterUpdateRequestDTO::class))
        ),
        tags: ['Car Database Engine Filters'],
        responses: [
            new OA\Response(response: 200, description: 'Updated', content: new Model(type: EngineFilterUpdateResponseDTO::class)),
            new OA\Response(response: 400, description: 'Bad request', content: new Model(type: ErrorCollection::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/engine-filters/{engineFilterId}', name: 'car_database_engine_filter_update', methods: ['PUT'])]
    public function update(Request $request, string $engineFilterId): JsonResponse
    {
        $this->requireAdminUser();

        $engineFilter = $this->engineFilterRepository->find($engineFilterId);
        if ($engineFilter === null) {
            throw new NotFoundHttpException();
        }

        try {
            $updateRequestDTO = $this->dtoValueResolver->resolveRequest($request, EngineFilterUpdateRequestDTO::class);
            $this->dtoValueResolver->validateDTO($updateRequestDTO);

            $engine = $this->engineRepository->find($updateRequestDTO->getEngineId());
            $filter = $this->filterRepository->find($updateRequestDTO->getFilterId());

            if ($engine === null || $filter === null) {
                throw new BadRequestHttpException('Engine or filter not found.');
            }

            $engineFilter = $this->engineFilterService->updateEngineFilter(
                $engineFilter,
                $engine,
                $filter,
                $updateRequestDTO->isPrimary(),
                $updateRequestDTO->getSource(),
            );

            return $this->json($this->dtoFactory->createEngineFilterUpdateResponseDTO($engineFilter));
        } catch (ValidationException $exception) {
            return $this->responseFactory->createResponseErrorCollection($exception->getErrorCollection());
        } catch (InvalidDataException) {
            throw new BadRequestHttpException();
        } catch (Throwable $exception) {
            throw new ServerErrorHttpException($exception->getMessage(), $exception);
        }
    }

    #[OA\Get(
        security: [[
            'Bearer' => [],
        ]],
        tags: ['Car Database Engine Filters'],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new Model(type: EngineFilterInfoResponseDTO::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/engine-filters/{engineFilterId}', name: 'car_database_engine_filter_info', methods: ['GET'])]
    public function info(string $engineFilterId): JsonResponse
    {
        $this->requireAdminUser();

        $engineFilter = $this->engineFilterRepository->find($engineFilterId);
        if ($engineFilter === null) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->dtoFactory->createEngineFilterInfoResponseDTO($engineFilter));
    }

    #[OA\Get(
        security: [[
            'Bearer' => [],
        ]],
        tags: ['Car Database Engine Filters'],
        parameters: [
            new OA\Parameter(name: self::FILTER_ENGINE_ID_KEY, description: 'Filter by engine ID', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: self::FILTER_FILTER_ID_KEY, description: 'Filter by filter ID', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: self::FILTER_IS_PRIMARY_KEY, description: 'Filter by primary flag', in: 'query', required: false, schema: new OA\Schema(type: 'boolean')),
            new OA\Parameter(name: ApiGridPropertyHelper::PAGE_KEY, description: 'Number of page, default value 1', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 1),
            new OA\Parameter(
                name: ApiGridPropertyHelper::MAX_RESULTS_KEY,
                description: 'Number of items on the page, default value ' . ApiGridPropertyHelper::MAX_RESULTS_DEFAULT_VALUE,
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer'),
                example: ApiGridPropertyHelper::MAX_RESULTS_DEFAULT_VALUE,
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::SORT_KEY,
                description: 'Sorting by values, default value createdAt',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'createdAt',
            ),
            new OA\Parameter(
                name: ApiGridPropertyHelper::ORDER_KEY,
                description: 'Select ordering, default value ASC, values: ASC, DESC',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'ASC',
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new Model(type: EngineFilterListResponseDTO::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/engine-filters', name: 'car_database_engine_filter_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            try {
                $engineId = $request->query->get(self::FILTER_ENGINE_ID_KEY);
                assert(is_string($engineId));
                $qb->andWhere($qb->expr()->eq(EngineFilterRepository::ALIAS . '.engine', ':engineId'));
                $qb->setParameter('engineId', $engineId);
            } catch (Throwable) {
                // pass
            }

            try {
                $filterId = $request->query->get(self::FILTER_FILTER_ID_KEY);
                assert(is_string($filterId));
                $qb->andWhere($qb->expr()->eq(EngineFilterRepository::ALIAS . '.filter', ':filterId'));
                $qb->setParameter('filterId', $filterId);
            } catch (Throwable) {
                // pass
            }

            try {
                $isPrimary = $request->query->get(self::FILTER_IS_PRIMARY_KEY);

                if ($isPrimary !== null) {
                    $value = filter_var($isPrimary, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

                    if ($value !== null) {
                        $qb->andWhere($qb->expr()->eq(EngineFilterRepository::ALIAS . '.isPrimary', ':isPrimary'));
                        $qb->setParameter('isPrimary', $value);
                    }
                }
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::ASC);
        $sortEnum = $this->apiGridPropertyHelper->createSortEnum($request, EngineFilterGridSortEnum::class, EngineFilterGridSortEnum::CREATED_AT);
        $engineFilterQueryBuilder = $this->engineFilterRepository->getQueryBuilderWithAlias();
        $engineFilterPaginator = $this->apiGridManager->createPaginator($engineFilterQueryBuilder, $queryModifier);

        /** @var \App\CarDatabase\DBAL\Entity\EngineFilter[] $engineFilters */
        $engineFilters = $this->apiGridManager->fetchData(
            $engineFilterQueryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );

        $pageCount = $this->apiGridPropertyHelper->createPageCount(
            $engineFilterPaginator->count(),
            $maxResults
        );

        return $this->json($this->dtoFactory->createEngineFilterListResponseDTO($engineFilters, $pageCount));
    }

    #[OA\Delete(
        security: [[
            'Bearer' => [],
        ]],
        tags: ['Car Database Engine Filters'],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new Model(type: EngineFilterDeleteResponseDTO::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/engine-filters/{engineFilterId}', name: 'car_database_engine_filter_delete', methods: ['DELETE'])]
    public function delete(string $engineFilterId): JsonResponse
    {
        $this->requireAdminUser();

        $engineFilter = $this->engineFilterRepository->find($engineFilterId);
        if ($engineFilter === null) {
            throw new NotFoundHttpException();
        }

        $this->engineFilterService->deleteEngineFilter($engineFilter);

        return $this->json($this->dtoFactory->createEngineFilterDeleteResponseDTO());
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
