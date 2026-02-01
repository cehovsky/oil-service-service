<?php

declare(strict_types=1);

namespace App\Modules\CarApp\Controller;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\Domain\Exception\ServerErrorHttpException;
use App\Modules\CarApp\DTO\CarAppRouteListResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\OilService\DBAL\Repository\RouteRepository;
use DateTimeImmutable;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class CarAppRouteController extends AbstractController
{
    public function __construct(
        private readonly DTOFactory $dtoFactory,
        private readonly RouteRepository $routeRepository,
        private readonly Security $security,
    ) {
    }

    #[OA\Get(
        security: [
            [
                'Bearer' => []
            ],
        ],
        tags: [
            'Car App Routes',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new Model(
                    type: CarAppRouteListResponseDTO::class
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
        '/car-app/routes',
        name: 'oil_service_car_app_routes_today',
        methods: ['GET']
    )]
    public function listToday(): JsonResponse
    {
        $user = $this->requireActiveUser();

        try {
            $today = DateTimeImmutable::createFromFormat('!Y-m-d', (new DateTimeImmutable())->format('Y-m-d'));

            if ($today === false) {
                throw new ServerErrorHttpException('Invalid date.');
            }

            $routes = $this->routeRepository->findActiveRoutesForUserOnDate($user, $today);

            $responseDTO = $this->dtoFactory->createCarAppRouteListResponseDTO($routes, 1);

            return $this->json($responseDTO);
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
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
}
