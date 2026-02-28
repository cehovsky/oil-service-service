<?php

declare(strict_types=1);

namespace App\Modules\CarApp\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Domain\Exception\ServerErrorHttpException;
use App\Modules\Sepno\DTO\SepnoRecordCurrentResponseDTO;
use App\Modules\Sepno\DTO\SepnoRecordInfoResponseDTO;
use App\Modules\Sepno\Factory\DTOFactory;
use App\Sepno\SepnoService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

class CarAppSepnoController extends AbstractController
{
    public function __construct(
        private readonly SepnoService $sepnoService,
        private readonly DTOFactory $dtoFactory,
        private readonly Security $security,
    ) {
    }

    #[OA\Get(
        security: [[ 'Bearer' => [] ]],
        tags: ['Car App SEPNo'],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new Model(type: SepnoRecordCurrentResponseDTO::class)),
        ]
    )]
    #[Route('/car-app/routes/{routeId}/sepno-record', name: 'car_app_route_sepno_current', methods: ['GET'])]
    public function current(string $routeId): JsonResponse
    {
        $user = $this->requireActiveUser();
        $route = $this->sepnoService->findRouteById($routeId);
        $this->assertUserHasAccessToRoute($route, $user);

        $record = $this->sepnoService->getCurrentForRoute($route);

        return $this->json($this->dtoFactory->createSepnoRecordCurrentResponseDTO($record));
    }

    #[OA\Post(
        security: [[ 'Bearer' => [] ]],
        tags: ['Car App SEPNo'],
        responses: [
            new OA\Response(response: 200, description: 'Success', content: new Model(type: SepnoRecordInfoResponseDTO::class)),
        ]
    )]
    #[Route('/car-app/routes/{routeId}/sepno-records', name: 'car_app_route_sepno_create', methods: ['POST'])]
    public function create(string $routeId): JsonResponse
    {
        $user = $this->requireActiveUser();
        $route = $this->sepnoService->findRouteById($routeId);
        $this->assertUserHasAccessToRoute($route, $user);

        $record = $this->sepnoService->createAndSend($route, 'car_app', $user);

        return $this->json($this->dtoFactory->createSepnoRecordInfoResponseDTO($record));
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

    private function assertUserHasAccessToRoute(\App\OilService\DBAL\Entity\Route $route, AuthUser $user): void
    {
        foreach ($route->getRouteUsers() as $routeUser) {
            if ($routeUser->getUser()->getId()->toRfc4122() === $user->getId()->toRfc4122()) {
                return;
            }
        }

        throw new AccessDeniedHttpException('User does not have access to this route.');
    }
}
