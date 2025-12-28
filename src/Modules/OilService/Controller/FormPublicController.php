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
use App\Modules\OilService\Factory\DTOFactory;
use App\OilService\FormService;
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
    ) {
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
            'OilService',
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
        '/oil-service/form',
        name: 'oil_service_form_create',
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
}
