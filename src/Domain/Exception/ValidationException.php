<?php

namespace App\Domain\Exception;

use App\Domain\Error\ErrorCollection;
use Throwable;

class ValidationException extends CommonException
{
    private ErrorCollection $errorCollection;

    public function __construct(
        Throwable|string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ErrorCollection $errorCollection = new ErrorCollection(),
    ) {
        $this->errorCollection = $errorCollection;

        parent::__construct($message, $code, $previous);
    }

    public function getErrorCollection(): ErrorCollection
    {
        return $this->errorCollection;
    }
}
