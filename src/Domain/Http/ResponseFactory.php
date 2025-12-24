<?php

declare(strict_types=1);

namespace App\Domain\Http;

use App\Domain\DTOValueResolver;
use App\Domain\Error\ErrorCollection;
use App\Domain\Exception\ServerErrorHttpException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseFactory
{
    /**
     * @param array<string, string|int> $data
     */
    public function createSuccessResponse(array $data = []): JsonResponse
    {
        return new JsonResponse(array_merge(['result' => DTOValueResolver::RESULT_SUCCESS], $data), 200);
    }

    /**
     * @throws ServerErrorHttpException
     */
    public function createResponseErrorCollection(ErrorCollection $errorCollection): JsonResponse
    {
        try {
            return new JsonResponse(
                $errorCollection->toResponseArray(),
                Response::HTTP_BAD_REQUEST,
            );
        } catch (InvalidArgumentException) {
            throw new ServerErrorHttpException();
        }
    }

    public function createBadRequestResponse(string $errorCode, string $message): JsonResponse
    {
        return new JsonResponse([
            'result' => DTOValueResolver::RESULT_ERROR,
            'errorCode' => $errorCode,
            'message' => $message,
        ], 400);
    }

    public function createNotFoundResponse(?string $message = null): JsonResponse
    {
        return new JsonResponse([
            'result' => DTOValueResolver::RESULT_ERROR,
            'errorCode' => 'notFound',
            'message' => $message ?: 'Not found',
        ], 404);
    }

    public function createServerErrorResponse(string $message): JsonResponse
    {
        return new JsonResponse([
            'result' => DTOValueResolver::RESULT_ERROR,
            'errorCode' => 'serverError',
            'message' => $message,
        ], 500);
    }
}
