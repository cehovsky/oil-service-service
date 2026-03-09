<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\CarDatabase\DBAL\Repository\EngineRepository;
use App\CarDatabase\EngineService;
use App\Domain\ApiGrid\ApiGridManager;
use App\Domain\ApiGrid\ApiGridPropertyHelper;
use App\Domain\ApiGrid\OrderEnum;
use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Modules\CarDatabase\DTO\EngineCreateRequestDTO;
use App\Modules\CarDatabase\DTO\EngineCreateResponseDTO;
use App\Modules\CarDatabase\DTO\EngineDeleteResponseDTO;
use App\Modules\CarDatabase\DTO\EngineInfoResponseDTO;
use App\Modules\CarDatabase\DTO\EngineListResponseDTO;
use App\Modules\CarDatabase\DTO\EngineUpdateRequestDTO;
use App\Modules\CarDatabase\DTO\EngineUpdateResponseDTO;
use App\Modules\CarDatabase\Factory\DTOFactory;
use App\Modules\CarDatabase\Grid\Enum\EngineGridSortEnum;
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

class EngineController extends AbstractController
{
    private const string FILTER_MANUFACTURER_KEY = 'manufacturer';
    private const string FILTER_MODEL_KEY = 'model';
    private const string FILTER_ENGINE_CODE_KEY = 'engineCode';
    private const string FILTER_ENGINE_FAMILY_KEY = 'engineFamily';
    private const string FILTER_FUEL_KEY = 'fuel';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly EngineRepository $engineRepository,
        private readonly ApiGridPropertyHelper $apiGridPropertyHelper,
        private readonly ApiGridManager $apiGridManager,
        private readonly Security $security,
        private readonly EngineService $engineService,
    ) {
    }

    #[OA\Post(
        security: [[
            'Bearer' => [],
        ]],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: new Model(type: EngineCreateRequestDTO::class))
        ),
        tags: ['Car Database Engines'],
        responses: [
            new OA\Response(response: 200, description: 'Created', content: new Model(type: EngineCreateResponseDTO::class)),
            new OA\Response(response: 400, description: 'Bad request', content: new Model(type: ErrorCollection::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/engines', name: 'car_database_engine_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        try {
            $createRequestDTO = $this->dtoValueResolver->resolveRequest($request, EngineCreateRequestDTO::class);
            $this->dtoValueResolver->validateDTO($createRequestDTO);

            $engine = $this->engineService->createEngine(
                $createRequestDTO->getManufacturer(),
                $createRequestDTO->getModel(),
                $createRequestDTO->getGeneration(),
                $createRequestDTO->getEngineCode(),
                $createRequestDTO->getEngineFamily(),
                $createRequestDTO->getDisplacementCc(),
                $createRequestDTO->getPowerKw(),
                $createRequestDTO->getFuel(),
                $createRequestDTO->getEmissionStandard(),
                $createRequestDTO->getProductionFromYear(),
                $createRequestDTO->getProductionToYear(),
                $createRequestDTO->getOilCapacityL(),
                $createRequestDTO->getOilCapacityNote(),
                $createRequestDTO->getOilViscosity(),
                $createRequestDTO->getOilSpecification(),
                $createRequestDTO->getOilIntervalKm(),
                $createRequestDTO->getOilIntervalMonths(),
                $createRequestDTO->getOilDrainPlugTorqueNm(),
                $createRequestDTO->getOilFilterTorqueNm(),
                $createRequestDTO->getSparkPlugTorqueNm(),
                $createRequestDTO->getSource(),
                $createRequestDTO->getConfidence(),
                $createRequestDTO->getNotes(),
            );

            return $this->json($this->dtoFactory->createEngineCreateResponseDTO($engine));
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
            content: new OA\JsonContent(ref: new Model(type: EngineUpdateRequestDTO::class))
        ),
        tags: ['Car Database Engines'],
        responses: [
            new OA\Response(response: 200, description: 'Updated', content: new Model(type: EngineUpdateResponseDTO::class)),
            new OA\Response(response: 400, description: 'Bad request', content: new Model(type: ErrorCollection::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/engines/{engineId}', name: 'car_database_engine_update', methods: ['PUT'])]
    public function update(Request $request, string $engineId): JsonResponse
    {
        $this->requireAdminUser();

        $engine = $this->engineRepository->find($engineId);
        if ($engine === null) {
            throw new NotFoundHttpException();
        }

        try {
            $updateRequestDTO = $this->dtoValueResolver->resolveRequest($request, EngineUpdateRequestDTO::class);
            $this->dtoValueResolver->validateDTO($updateRequestDTO);

            $engine = $this->engineService->updateEngine(
                $engine,
                $updateRequestDTO->getManufacturer(),
                $updateRequestDTO->getModel(),
                $updateRequestDTO->getGeneration(),
                $updateRequestDTO->getEngineCode(),
                $updateRequestDTO->getEngineFamily(),
                $updateRequestDTO->getDisplacementCc(),
                $updateRequestDTO->getPowerKw(),
                $updateRequestDTO->getFuel(),
                $updateRequestDTO->getEmissionStandard(),
                $updateRequestDTO->getProductionFromYear(),
                $updateRequestDTO->getProductionToYear(),
                $updateRequestDTO->getOilCapacityL(),
                $updateRequestDTO->getOilCapacityNote(),
                $updateRequestDTO->getOilViscosity(),
                $updateRequestDTO->getOilSpecification(),
                $updateRequestDTO->getOilIntervalKm(),
                $updateRequestDTO->getOilIntervalMonths(),
                $updateRequestDTO->getOilDrainPlugTorqueNm(),
                $updateRequestDTO->getOilFilterTorqueNm(),
                $updateRequestDTO->getSparkPlugTorqueNm(),
                $updateRequestDTO->getSource(),
                $updateRequestDTO->getConfidence(),
                $updateRequestDTO->getNotes(),
            );

            return $this->json($this->dtoFactory->createEngineUpdateResponseDTO($engine));
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
        tags: ['Car Database Engines'],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new Model(type: EngineInfoResponseDTO::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/engines/{engineId}', name: 'car_database_engine_info', methods: ['GET'])]
    public function info(string $engineId): JsonResponse
    {
        $this->requireAdminUser();

        $engine = $this->engineRepository->find($engineId);
        if ($engine === null) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->dtoFactory->createEngineInfoResponseDTO($engine));
    }

    #[OA\Get(
        security: [[
            'Bearer' => [],
        ]],
        tags: ['Car Database Engines'],
        parameters: [
            new OA\Parameter(name: self::FILTER_MANUFACTURER_KEY, description: 'Filter by manufacturer (contains)', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: self::FILTER_MODEL_KEY, description: 'Filter by model (contains)', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: self::FILTER_ENGINE_CODE_KEY, description: 'Filter by engine code (contains)', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: self::FILTER_ENGINE_FAMILY_KEY, description: 'Filter by engine family (contains)', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: self::FILTER_FUEL_KEY, description: 'Filter by fuel (equals)', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
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
                description: 'Sorting by values, default value manufacturer',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'manufacturer',
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
            new OA\Response(response: 200, description: 'Success', content: new Model(type: EngineListResponseDTO::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/engines', name: 'car_database_engine_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $queryModifier = function (QueryBuilder $qb) use ($request): void {
            try {
                $manufacturer = $request->query->get(self::FILTER_MANUFACTURER_KEY);
                assert(is_string($manufacturer));
                $qb->andWhere($qb->expr()->like(EngineRepository::ALIAS . '.manufacturer', ':manufacturer'));
                $qb->setParameter('manufacturer', '%' . $manufacturer . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $model = $request->query->get(self::FILTER_MODEL_KEY);
                assert(is_string($model));
                $qb->andWhere($qb->expr()->like(EngineRepository::ALIAS . '.model', ':model'));
                $qb->setParameter('model', '%' . $model . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $engineCode = $request->query->get(self::FILTER_ENGINE_CODE_KEY);
                assert(is_string($engineCode));
                $qb->andWhere($qb->expr()->like(EngineRepository::ALIAS . '.engineCode', ':engineCode'));
                $qb->setParameter('engineCode', '%' . $engineCode . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $engineFamily = $request->query->get(self::FILTER_ENGINE_FAMILY_KEY);
                assert(is_string($engineFamily));
                $qb->andWhere($qb->expr()->like(EngineRepository::ALIAS . '.engineFamily', ':engineFamily'));
                $qb->setParameter('engineFamily', '%' . $engineFamily . '%');
            } catch (Throwable) {
                // pass
            }

            try {
                $fuel = $request->query->get(self::FILTER_FUEL_KEY);
                assert(is_string($fuel));
                $qb->andWhere($qb->expr()->eq(EngineRepository::ALIAS . '.fuel', ':fuel'));
                $qb->setParameter('fuel', $fuel);
            } catch (Throwable) {
                // pass
            }
        };

        $maxResults = $this->apiGridPropertyHelper->createMaxResults($request);
        $firstResult = $this->apiGridPropertyHelper->createfirstResult($request, $maxResults);
        $orderEnum = $this->apiGridPropertyHelper->createOrderEnum($request, OrderEnum::ASC);
        $sortEnum = $this->apiGridPropertyHelper->createSortEnum($request, EngineGridSortEnum::class, EngineGridSortEnum::MANUFACTURER);
        $engineQueryBuilder = $this->engineRepository->getQueryBuilderWithAlias();
        $enginePaginator = $this->apiGridManager->createPaginator($engineQueryBuilder, $queryModifier);

        /** @var \App\CarDatabase\DBAL\Entity\Engine[] $engines */
        $engines = $this->apiGridManager->fetchData(
            $engineQueryBuilder,
            $sortEnum,
            $orderEnum,
            $firstResult,
            $maxResults,
            $queryModifier
        );

        $pageCount = $this->apiGridPropertyHelper->createPageCount(
            $enginePaginator->count(),
            $maxResults
        );

        return $this->json($this->dtoFactory->createEngineListResponseDTO($engines, $pageCount));
    }

    #[OA\Delete(
        security: [[
            'Bearer' => [],
        ]],
        tags: ['Car Database Engines'],
        responses: [
            new OA\Response(response: 200, description: 'Deleted', content: new Model(type: EngineDeleteResponseDTO::class)),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 500, description: 'Server Error'),
        ]
    )]
    #[Route('/car-database/engines/{engineId}', name: 'car_database_engine_delete', methods: ['DELETE'])]
    public function delete(string $engineId): JsonResponse
    {
        $this->requireAdminUser();

        $engine = $this->engineRepository->find($engineId);
        if ($engine === null) {
            throw new NotFoundHttpException();
        }

        $this->engineService->deleteEngine($engine);

        return $this->json($this->dtoFactory->createEngineDeleteResponseDTO());
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
