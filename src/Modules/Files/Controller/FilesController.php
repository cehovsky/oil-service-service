<?php

declare(strict_types=1);

namespace App\Modules\Files\Controller;

use App\Auth\DBAL\Entity\User;
use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\ServerErrorHttpException;
use App\Domain\Exception\ValidationException;
use App\Domain\Http\ResponseFactory;
use App\Files\FileDownloadFailedException;
use App\Files\FileManager;
use App\Files\FileUploadFailedException;
use App\Modules\Files\DTO\FilesUploadRequestDTO;
use App\Modules\Files\DTO\FileUploadResponseDTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

class FilesController extends AbstractController
{
    public const string FILES_DOWNLOAD_URL = '/files/download/';

    public function __construct(
        private readonly DTOValueResolver $dtoValueResolver,
        private readonly ResponseFactory $responseFactory,
        private readonly FileManager $fileManager,
        private readonly LoggerInterface $logger,
        private readonly Security $security,
    ) {
    }

    #[OA\Post(
        description: 'Uploads file and returns a file handle.',
        security: [['Bearer' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: new Model(type: FilesUploadRequestDTO::class),
            ),
        ),
        tags: ['Files'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Files were successfully uploaded',
                content: new OA\JsonContent(
                    ref: new Model(type: FileUploadResponseDTO::class),
                ),
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new Model(type: ErrorCollection::class),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 500, description: 'Server Error'),
        ],
    )]
    #[Route(path: '/files/upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new ServerErrorHttpException();
        }

        try {
            $requestDTO = $this->dtoValueResolver->resolveRequest($request, FilesUploadRequestDTO::class);
        } catch (ValidationException $e) {
            return $this->responseFactory->createResponseErrorCollection(
                $e->getErrorCollection(),
            );
        } catch (InvalidDataException) {
            return $this->responseFactory->createBadRequestResponse('invalidBody', 'Request body is invalid');
        }

        try {
            $file = $this->fileManager->saveFileAndUpload(
                $requestDTO->getFileName(),
                $requestDTO->getContentsAsBinary(),
                $user,
            );
        } catch (FileUploadFailedException $e) {
            $this->logger->error($e);

            return $this->responseFactory->createServerErrorResponse('File upload failed: ' . $e);
        }

        $responseDTO = new FileUploadResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            $file->getFileName(),
            (string) $file->getId(),
        );

        return $this->json($responseDTO);
    }

    #[OA\Get(
        description: 'Downloads a file by its handle.',
        tags: ['Files'],
        parameters: [
            new OA\Parameter(
                name: 'handle',
                description: 'File handle. Not to be confused with file name.',
                in: 'path',
                example: '248195a6-8ea5-493e-bb8e-5ef5d56345d0',
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Binary file contents',
                content: new OA\MediaType('application/octet-stream'),
            ),
            new OA\Response(
                response: 400,
                description: 'Bad request',
                content: new Model(type: ErrorCollection::class),
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not Found'),
            new OA\Response(response: 500, description: 'Server Error'),
        ],
    )]
    #[Route(path: self::FILES_DOWNLOAD_URL . '{handle}', methods: ['GET'])]
    public function download(string $handle): Response | JsonResponse
    {
        $file = $this->fileManager->getFileEntity($handle);

        if ($file === null) {
            return $this->responseFactory->createNotFoundResponse();
        }

        try {
            return new Response($this->fileManager->downloadFileContents($file), headers: [
                'Content-Description' => 'File Transfer',
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $file->getFileName(),
                'Connection' => 'Keep-Alive',
                'Content-Length' => $file->getSize(),
            ]);
        } catch (FileDownloadFailedException $e) {
            $this->logger->error($e, ['fileHandle' => $handle]);

            return $this->responseFactory->createNotFoundResponse(
                'Not found on the file server. Maybe try again later.',
            );
        } catch (Throwable $e) {
            return $this->responseFactory->createServerErrorResponse($e->getMessage());
        }
    }
}
