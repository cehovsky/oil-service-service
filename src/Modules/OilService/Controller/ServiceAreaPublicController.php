<?php

declare(strict_types=1);

namespace App\Modules\OilService\Controller;

use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Modules\OilService\DTO\ServiceAreaAddressCheckRequestDTO;
use App\Modules\OilService\DTO\ServiceAreaAddressCheckResponseDTO;
use App\Modules\OilService\DTO\ServiceAreaPolygonPointDTO;
use App\Modules\OilService\DTO\ServiceAreaPolygonResponseDTO;
use App\OilService\ServiceArea\ServiceAreaAddressEvaluationService;
use App\OilService\ServiceArea\ServiceAreaPolygonProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class ServiceAreaPublicController extends AbstractController
{
    public function __construct(
        private readonly ServiceAreaPolygonProvider $polygonProvider,
        private readonly ServiceAreaAddressEvaluationService $addressEvaluationService,
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly ResponseFactory $responseFactory,
    ) {
    }

    #[OA\Get(
        tags: ['Service Area'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Service area polygon coordinates',
                content: new Model(type: ServiceAreaPolygonResponseDTO::class),
            ),
            new OA\Response(response: 500, description: 'Server Error'),
        ],
    )]
    #[Route(
        '/oil-service/service-area/polygon',
        name: 'oil_service_service_area_polygon_public',
        methods: ['GET']
    )]
    public function polygon(): JsonResponse
    {
        try {
            $points = array_map(
                static fn (array $point): ServiceAreaPolygonPointDTO => new ServiceAreaPolygonPointDTO(
                    $point['latitude'],
                    $point['longitude'],
                ),
                $this->polygonProvider->getPolygonCoordinates(),
            );

            $response = $this->json(new ServiceAreaPolygonResponseDTO('success', time(), $points));
            $response->setPublic();
            $response->setMaxAge(6 * 60 * 60);
            $response->setSharedMaxAge(6 * 60 * 60);

            return $response;
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
    }

    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(ref: new Model(type: ServiceAreaAddressCheckRequestDTO::class)),
        ),
        tags: ['Service Area'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Address evaluation against service area',
                content: new Model(type: ServiceAreaAddressCheckResponseDTO::class),
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new Model(type: ErrorCollection::class),
            ),
            new OA\Response(response: 500, description: 'Server Error'),
        ],
    )]
    #[Route(
        '/oil-service/service-area/address-check',
        name: 'oil_service_service_area_address_check_public',
        methods: ['POST']
    )]
    public function checkAddress(Request $request): JsonResponse
    {
        try {
            $requestDTO = $this->dtoValueResolver->resolveRequest($request, ServiceAreaAddressCheckRequestDTO::class);
            $this->dtoValueResolver->validateDTO($requestDTO);

            $evaluation = $this->addressEvaluationService->evaluateAddress($requestDTO->getAddress());

            return $this->json(
                new ServiceAreaAddressCheckResponseDTO(
                    'success',
                    time(),
                    $evaluation->isRecognized(),
                    $evaluation->getWithinServiceArea(),
                    $evaluation->getLatitude(),
                    $evaluation->getLongitude(),
                    $evaluation->getMessage(),
                )
            );
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection($e->getErrorCollection());
        } catch (InvalidDataException) {
            throw new BadRequestHttpException();
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
    }
}
