<?php

declare(strict_types=1);

namespace App\Modules\Sepno\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Domain\DTOValueResolver;
use App\Domain\Exception\ServerErrorHttpException;
use App\Modules\Sepno\DTO\SepnoRecordCloseRequestDTO;
use App\Modules\Sepno\DTO\SepnoRecordCreateRequestDTO;
use App\Modules\Sepno\DTO\SepnoRecordCurrentResponseDTO;
use App\Modules\Sepno\DTO\SepnoRecordInfoResponseDTO;
use App\Modules\Sepno\DTO\SepnoRecordListResponseDTO;
use App\Modules\Sepno\Factory\DTOFactory;
use App\Sepno\DBAL\Enum\SepnoRecordStatusEnum;
use App\Sepno\DBAL\Repository\SepnoRecordRepository;
use App\Sepno\SepnoService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class SepnoController extends AbstractController
{
    public function __construct(
        private readonly SepnoService $sepnoService,
        private readonly SepnoRecordRepository $sepnoRecordRepository,
        private readonly DTOFactory $dtoFactory,
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly Security $security,
    ) {
    }

    #[OA\Get(
        security: [[ 'Bearer' => [] ]],
        tags: ['SEPNo'],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new Model(type: SepnoRecordListResponseDTO::class)),
        ]
    )]
    #[Route('/oil-service/sepno-records', name: 'oil_service_sepno_record_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $this->requireAdminUser();

        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = max(1, min(100, (int) $request->query->get('perPage', 30)));
        $routeId = $request->query->get('routeId');
        $statusRaw = $request->query->get('status');

        $status = null;
        if (is_string($statusRaw) && $statusRaw !== '') {
            $status = SepnoRecordStatusEnum::tryFrom($statusRaw);
        }

        $result = $this->sepnoRecordRepository->findPaged(
            $page,
            $perPage,
            is_string($routeId) && $routeId !== '' ? $routeId : null,
            $status,
        );

        $total = $result['total'];
        $pageCount = (int) max(1, (int) ceil($total / $perPage));

        return $this->json(
            $this->dtoFactory->createSepnoRecordListResponseDTO($result['records'], $pageCount)
        );
    }

    #[OA\Get(
        security: [[ 'Bearer' => [] ]],
        tags: ['SEPNo'],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new Model(type: SepnoRecordInfoResponseDTO::class)),
        ]
    )]
    #[Route('/oil-service/sepno-records/{recordId}', name: 'oil_service_sepno_record_info', methods: ['GET'])]
    public function info(string $recordId): JsonResponse
    {
        $this->requireAdminUser();

        $record = $this->sepnoService->findRecordById($recordId);

        return $this->json($this->dtoFactory->createSepnoRecordInfoResponseDTO($record));
    }

    #[OA\Get(
        security: [[ 'Bearer' => [] ]],
        tags: ['SEPNo'],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new Model(type: SepnoRecordCurrentResponseDTO::class)),
        ]
    )]
    #[Route('/oil-service/routes/{routeId}/sepno-record', name: 'oil_service_route_sepno_current', methods: ['GET'])]
    public function routeCurrent(string $routeId): JsonResponse
    {
        $this->requireAdminUser();

        $route = $this->sepnoService->findRouteById($routeId);
        $record = $this->sepnoService->getCurrentForRoute($route);

        return $this->json($this->dtoFactory->createSepnoRecordCurrentResponseDTO($record));
    }

    #[OA\Post(
        security: [[ 'Bearer' => [] ]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: SepnoRecordCreateRequestDTO::class))),
        tags: ['SEPNo'],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new Model(type: SepnoRecordInfoResponseDTO::class)),
        ]
    )]
    #[Route('/oil-service/routes/{routeId}/sepno-records', name: 'oil_service_route_sepno_create', methods: ['POST'])]
    public function create(Request $request, string $routeId): JsonResponse
    {
        $user = $this->requireAdminUser();
        $route = $this->sepnoService->findRouteById($routeId);

        $estimatedWasteKg = null;

        if ($this->hasJsonBody($request)) {
            $createRequestDTO = $this->dtoValueResolver->resolveRequest($request, SepnoRecordCreateRequestDTO::class);
            $this->dtoValueResolver->validateDTO($createRequestDTO);
            $estimatedWasteKg = $createRequestDTO->getEstimatedWasteKg();
        }

        $record = $this->sepnoService->createAndSend($route, 'admin', $user, $estimatedWasteKg);

        return $this->json($this->dtoFactory->createSepnoRecordInfoResponseDTO($record));
    }

    #[OA\Post(
        security: [[ 'Bearer' => [] ]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(ref: new Model(type: SepnoRecordCloseRequestDTO::class))),
        tags: ['SEPNo'],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new Model(type: SepnoRecordInfoResponseDTO::class)),
        ]
    )]
    #[Route('/oil-service/sepno-records/{recordId}/close', name: 'oil_service_sepno_record_close', methods: ['POST'])]
    public function close(Request $request, string $recordId): JsonResponse
    {
        $user = $this->requireAdminUser();
        $record = $this->sepnoService->findRecordById($recordId);

        $actualWasteKg = null;

        if ($this->hasJsonBody($request)) {
            $closeRequestDTO = $this->dtoValueResolver->resolveRequest($request, SepnoRecordCloseRequestDTO::class);
            $this->dtoValueResolver->validateDTO($closeRequestDTO);
            $actualWasteKg = $closeRequestDTO->getActualWasteKg();
        }

        try {
            $record = $this->sepnoService->closeRecord($record, $user, $actualWasteKg);
            return $this->json($this->dtoFactory->createSepnoRecordInfoResponseDTO($record));
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
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

    private function hasJsonBody(Request $request): bool
    {
        return trim((string) $request->getContent()) !== '';
    }
}
