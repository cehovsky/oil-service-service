<?php

declare(strict_types=1);

namespace App\Modules\OilService\Controller;

use App\Core\Service\DateTimeService;
use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Modules\OilService\DTO\OrderCreateRequestDTO;
use App\Modules\OilService\DTO\OrderCreateResponseDTO;
use App\Modules\OilService\DTO\AvailableTermListResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\OrderService;
use App\OilService\Term\TermAvailabilityPolicy;
use App\OilService\DBAL\Repository\TermRepository;
use DateTimeImmutable;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class OrderPublicController extends AbstractController
{
    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly OrderService $orderService,
        private readonly TermRepository $termRepository,
        private readonly TermAvailabilityPolicy $termAvailabilityPolicy,
        private readonly DateTimeService $dateTimeService,
    ) {
    }

    #[OA\Get(
        tags: [
            'Terms',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Available future terms ordered by date',
                content: new Model(
                    type: AvailableTermListResponseDTO::class
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/oil-service/terms/available',
        name: 'oil_service_available_terms',
        methods: ['GET']
    )]
    public function listAvailableTerms(): JsonResponse
    {
        try {
            $terms = $this->termRepository->findUpcomingAvailableTerms(
                $this->termAvailabilityPolicy->getMinimumAvailableDate()
            );

            $responseDTO = $this->dtoFactory->createAvailableTermListResponseDTO($terms);

            return $this->json($responseDTO);
        } catch (Throwable $e) {
            throw new ServerErrorHttpException($e->getMessage(), $e);
        }
    }

    /**
     * @throws ServerErrorHttpException
     */
    #[OA\Post(
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                ref: new Model(
                    type: OrderCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Orders',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order created successfully',
                content: new Model(
                    type: OrderCreateResponseDTO::class
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
                response: 500,
                description: 'Server Error'
            ),
        ]
    )]
    #[Route(
        '/oil-service/orders/submit',
        name: 'oil_service_order_submit',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        try {
            $orderCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                OrderCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($orderCreateRequestDTO);

            $this->orderService->createOrderWithUser(
                $orderCreateRequestDTO->getFullName(),
                $orderCreateRequestDTO->getPhone(),
                $orderCreateRequestDTO->getEmail(),
                $orderCreateRequestDTO->getCarModel(),
                $orderCreateRequestDTO->getLicensePlate(),
                $orderCreateRequestDTO->getAddress(),
                $orderCreateRequestDTO->getNote(),
                $orderCreateRequestDTO->getIsCompany(),
                $orderCreateRequestDTO->getCompanyName(),
                $orderCreateRequestDTO->getCompanyIdentificationNumber(),
                $orderCreateRequestDTO->getCompanyTaxId(),
                $orderCreateRequestDTO->getCompanyAddress(),
                $orderCreateRequestDTO->getOilChangeVehiclePhotoId(),
                $orderCreateRequestDTO->getVinPhotoId(),
                $orderCreateRequestDTO->getOldOilFilterPhotoId(),
                $orderCreateRequestDTO->getOldOilPhotoId(),
                $orderCreateRequestDTO->getOdometerPhotoId(),
                $orderCreateRequestDTO->getOtherPhotoIds(),
                OrderStatusEnum::from($orderCreateRequestDTO->getStatus()),
                RealizationTimeSlotEnum::from($orderCreateRequestDTO->getRealizationTimeSlot()),
                $this->dateTimeService->createDateFromString($orderCreateRequestDTO->getRealizationDate()),
                $orderCreateRequestDTO->getPriceListItemIds(),
                null,
            );

            $orderCreateResponseDTO = $this->dtoFactory->createOrderCreateResponseDTO();

            return $this->json($orderCreateResponseDTO);
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
}
