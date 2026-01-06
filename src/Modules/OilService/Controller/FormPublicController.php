<?php

declare(strict_types=1);

namespace App\Modules\OilService\Controller;

use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Modules\OilService\DTO\FormCreateRequestDTO;
use App\Modules\OilService\DTO\FormCreateResponseDTO;
use App\Modules\OilService\DTO\AvailableTermListResponseDTO;
use App\Modules\OilService\Factory\DTOFactory;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Enum\FormStatusEnum;
use App\OilService\FormService;
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

class FormPublicController extends AbstractController
{
    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly DTOFactory $dtoFactory,
        private readonly ResponseFactory $responseFactory,
        private readonly FormService $formService,
        private readonly TermRepository $termRepository,
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
            $terms = $this->termRepository->findUpcomingAvailableTerms(new DateTimeImmutable('tomorrow'));

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
                    type: FormCreateRequestDTO::class
                ),
            )
        ),
        tags: [
            'Forms',
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Form created successfully',
                content: new Model(
                    type: FormCreateResponseDTO::class
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
        '/oil-service/forms/submit',
        name: 'oil_service_form_submit',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        try {
            $formCreateRequestDTO = $this->dtoValueResolver->resolveRequest(
                $request,
                FormCreateRequestDTO::class
            );

            $this->dtoValueResolver->validateDTO($formCreateRequestDTO);

            $this->formService->createFormWithUser(
                $formCreateRequestDTO->getFullName(),
                $formCreateRequestDTO->getPhone(),
                $formCreateRequestDTO->getEmail(),
                $formCreateRequestDTO->getCarModel(),
                $formCreateRequestDTO->getLicensePlate(),
                $formCreateRequestDTO->getAddress(),
                $formCreateRequestDTO->getNote(),
                $formCreateRequestDTO->getIsCompany(),
                $formCreateRequestDTO->getCompanyName(),
                $formCreateRequestDTO->getCompanyIdentificationNumber(),
                $formCreateRequestDTO->getCompanyTaxId(),
                $formCreateRequestDTO->getCompanyAddress(),
                FormStatusEnum::from($formCreateRequestDTO->getStatus()),
                RealizationTimeSlotEnum::from($formCreateRequestDTO->getRealizationTimeSlot()),
                $this->createRealizationDate($formCreateRequestDTO->getRealizationDate()),
                null,
            );

            $formCreateResponseDTO = $this->dtoFactory->createFormCreateResponseDTO();

            return $this->json($formCreateResponseDTO);
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

    private function createRealizationDate(string $realizationDate): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $realizationDate);

        if ($date === false) {
            throw new InvalidDataException('Invalid realization date format.');
        }

        return $date;
    }
}
