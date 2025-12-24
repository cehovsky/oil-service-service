<?php

// @phpcs:disable PSR1.Files.SideEffects

declare(strict_types=1);

namespace App\Core\EventListener;

use App\Core\Factory\DTOFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class ExceptionListener
{
    private const KERNEL_ENVIRONMENT_DEV = 'dev';

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly DTOFactory $dtoFactory,
        private readonly LoggerInterface $logger
    ) {
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->kernel->getEnvironment() === self::KERNEL_ENVIRONMENT_DEV) {
            return;
        }

        $throwable = $event->getThrowable();

        if ($throwable instanceof NotFoundHttpException) {
            $responseCode = Response::HTTP_NOT_FOUND;
        } elseif ($throwable instanceof MethodNotAllowedHttpException) {
            $responseCode = Response::HTTP_NOT_FOUND;
        } elseif ($throwable instanceof BadRequestHttpException) {
            $responseCode = Response::HTTP_BAD_REQUEST;
        } elseif ($throwable instanceof UnauthorizedHttpException) {
            $responseCode = Response::HTTP_UNAUTHORIZED;
        } else {
            $this->logger->critical(
                $throwable->getMessage(),
                ['exception' => $throwable]
            );

            $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $event->setResponse(
            $this->createSerializedJsonResponse($responseCode)
        );
    }

    private function createSerializedJsonResponse(
        int $responseStatus
    ): JsonResponse {
        $encoder = new JsonEncoder();
        $normalizer = new GetSetMethodNormalizer();
        $serializer = new Serializer([$normalizer], [$encoder]);
        $result = Response::$statusTexts[$responseStatus] ??
            (Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR] ?? '');
        $responseDTO = $this->dtoFactory->createErrorResponseDTO($result);

        return new JsonResponse(
            $serializer->serialize($responseDTO, JsonEncoder::FORMAT),
            $responseStatus,
            [],
            true
        );
    }
}
