<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\CarDatabase\DBAL\Enum\FilterTypeEnum;
use App\CarDatabase\DBAL\Repository\FilterRepository;
use App\CarDatabase\FilterService;
use App\Domain\ApiGrid\ApiGridManager;
use App\Domain\ApiGrid\ApiGridPropertyHelper;
use App\Domain\ApiGrid\OrderEnum;
use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Modules\CarDatabase\DTO\FilterCreateRequestDTO;
use App\Modules\CarDatabase\DTO\FilterCreateResponseDTO;
use App\Modules\CarDatabase\DTO\FilterDeleteResponseDTO;
use App\Modules\CarDatabase\DTO\FilterInfoResponseDTO;
use App\Modules\CarDatabase\DTO\FilterListResponseDTO;
use App\Modules\CarDatabase\DTO\FilterUpdateRequestDTO;
use App\Modules\CarDatabase\DTO\FilterUpdateResponseDTO;
use App\Modules\CarDatabase\Factory\DTOFactory;
use App\Modules\CarDatabase\Grid\Enum\FilterGridSortEnum;
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

class FilterController extends AbstractController
{
    private const string FILTER_TYPE_KEY = 'filterType';
    private const string FILTER_MANUFACTURER_KEY = 'manufacturer';
    private const string FILTER_CODE_KEY = 'code';
    private const string FILTER_OEM_CODE_KEY = 'oemCode';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly FilterRepository $filterRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly FilterService $filterService,
    ) {
    }

    #[OA\Post(
        security: [[
            'Bearer' => [],
        ]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: new Model(type: FilterCreateRequestDTO::class))
        ),
        tags: ['Car Database Filters'],
        responses: [
            new OA\Response(response: 200, description: 'Created', content: new Model(type: FilterCreateResponseDTO::class)),
            new OA\Response(response: 400, description: 'Bad request', content: new Model(type: ErrorCollection::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/filters', name: 'car_database_filter_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $createRequestDTO = $this->dtoValueResolver->resolveRequest($request, FilterCreateRequestDTO::class);
            $this->dtoValueResolver->validateDTO($createRequestDTO);

            $filterType = FilterTypeEnum::from($createRequestDTO->getFilterType());

            $filter = $this->filterService->createFilter(
                $filterType,
                $createRequestDTO->getManufacturer(),
                $createRequestDTO->getCode(),
                $createRequestDTO->getOemCode(),
                $createRequestDTO->getThread(),
                $createRequestDTO->getHeightMm(),
                $createRequestDTO->getDiameterMm(),
                $createRequestDTO->getNotes(),
            );

            return $this->json($this->dtoFactory->createFilterCreateResponseDTO($filter));
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
            content: new OA\JsonContent(ref: new Model(type: FilterUpdateRequestDTO::class))
        ),
        tags: ['Car Database Filters'],
        responses: [
            new OA\Response(response: 200, description: 'Updated', content: new Model(type: FilterUpdateResponseDTO::class)),
            new OA\Response(response: 400, description: 'Bad request', content: new Model(type: ErrorCollection::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/filters/{filterId}', name: 'car_database_filter_update', methods: ['PUT'])]
    public function update(Request $request, string $filterId): JsonResponse
    {
        $this->requireAdminUser();

        $filter = $this->filterRepository->find($filterId);
        if ($filter === null) {
            throw new NotFoundHttpException();
        }

        try {
            $updateRequestDTO = $this->dtoValueResolver->resolveRequest($request, FilterUpdateRequestDTO::class);
            $this->dtoValueResolver->validateDTO($updateRequestDTO);

            $filterType = FilterTypeEnum::from($updateRequestDTO->getFilterType());

            $filter = $this->filterService->updateFilter(
                $filter,
                $filterType,
                $updateRequestDTO->getManufacturer(),
                $updateRequestDTO->getCode(),
                $updateRequestDTO->getOemCode(),
                $updateRequestDTO->getThread(),
                $updateRequestDTO->getHeightMm(),
                $updateRequestDTO->getDiameterMm(),
                $updateRequestDTO->getNotes(),
            );

            return $this->json($this->dtoFactory->createFilterUpdateResponseDTO($filter));
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
        tags: ['Car Database Filters'],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new Model(type: FilterInfoResponseDTO::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/filters/{filterId}', name: 'car_database_filter_info', methods: ['GET'])]
    public function info(string $filterId): JsonResponse
    {
        $this->requireAdminUser();

        $filter = $this->filterRepository->find($filterId);
        if ($filter === null) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->dtoFactory->createFilterInfoResponseDTO($filter));
    }

    #[OA\Get(
        security: [[
            'Bearer' => [],
        ]],
        tags: ['Car Database Filters'],
        parameters: [
            new OA\Parameter(name: self::FILTER_TYPE_KEY, description: 'Filter by type', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: FilterTypeEnum::VALUES), example: 'oil'),
            new OA\Parameter(name: self::FILTER_MANUFACTURER_KEY, description: 'Filter by manufacturer (contains)', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: self::FILTER_CODE_KEY, description: 'Filter by code (contains)', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: self::FILTER_OEM_CODE_KEY, description: 'Filter by OEM code (contains)', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: ApiGridPropertyHelper::PAGE_KEY, description: 'Number of page, default value 1', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 1),
            new OA\Parameter(name: ApiGridPropertyHelper::MAX_RESULTS_KEY, description: 'Number of items on the page, default value ' . ApiGridPropertyHelper::MAX_RESULTS_DEFAULT_VALUE, in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: ApiGridPropertyHelper::MAX_RESULTS_DEFAULT_VALUE),
            new OA\Parameter(name: ApiGridPropertyHelper::SORT_KEY, description: 'Sorting by values, default value code', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: 'code'),
            new OA\Parameter(name: ApiGridPropertyHelper::ORDER_KEY, description: 'Select ordering, default value ASC, values: ASC, DESC', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: 'ASC'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new Model(type: FilterListResponseDTO::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/filters', name: 'car_database_filter_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            try {
                $filterType = $request->query->get(self::FILTER_TYPE_KEY);
                assert(is_string($filterType));
                $filterEnum = FilterTypeEnum::tryFrom($filterType);

                if ($filterEnum !== null) {
                    $qb->andWhere($qb->expr()->eq(FilterRepository::ALIAS . '.filterType', ':filterType'));
                    $qb->setParameter('filterType', $filterEnum->value);
                }
            } catch (Throwable) {
                // pass
            }

            try {
                $manufacturer = $request->query->get(self::FILTER_MANUFACTURER_KEY);
                assert(is_string($manufacturer));
                $qb->andWhere($qb->expr()->like(FilterRepository::ALIAS . '.manufacturer', ':manufacturer'));
                $qb->setParameter('manufacturer', '%' . $manufacturer . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $code = $request->query->get(self::FILTER_CODE_KEY);
                assert(is_string($code));
                $qb->andWhere($qb->expr()->like(FilterRepository::ALIAS . '.code', ':code'));
                $qb->setParameter('code', '%' . $code . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $oemCode = $request->query->get(self::FILTER_OEM_CODE_KEY);
                assert(is_string($oemCode));
                $qb->andWhere($qb->expr()->like(FilterRepository::ALIAS . '.oemCode', ':oemCode'));
                $qb->setParameter('oemCode', '%' . $oemCode . '%');
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::ASC);
        $sortEnum = $this->apiGridPropertyHelper->createSortEnum($request, FilterGridSortEnum::class, FilterGridSortEnum::CODE);
        $filterQueryBuilder = $this->filterRepository->getQueryBuilderWithAlias();
        $filterPaginator = $this->apiGridManager->createPaginator($filterQueryBuilder, $queryModifier);

        $filters = $this->apiGridManager->fetchData(
            $filterQueryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );

        $pageCount = $this->apiGridPropertyHelper->createPageCount(
            $filterPaginator->count(),
            $maxResults
        );

        return $this->json($this->dtoFactory->createFilterListResponseDTO($filters, $pageCount));
    }

    #[OA\Delete(
        security: [[
            'Bearer' => [],
        ]],
        tags: ['Car Database Filters'],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new Model(type: FilterDeleteResponseDTO::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/filters/{filterId}', name: 'car_database_filter_delete', methods: ['DELETE'])]
    public function delete(string $filterId): JsonResponse
    {
        $this->requireAdminUser();

        $filter = $this->filterRepository->find($filterId);
        if ($filter === null) {
            throw new NotFoundHttpException();
        }

        $this->filterService->deleteFilter($filter);

        return $this->json($this->dtoFactory->createFilterDeleteResponseDTO());
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
