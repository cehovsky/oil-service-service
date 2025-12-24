<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ServerErrorHttpException extends HttpException
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        string $message = '',
        ?Throwable $previous = null,
        int $code = 0,
        array $headers = []
    ) {
        parent::__construct(500, $message, $previous, $headers, $code);
    }
}
