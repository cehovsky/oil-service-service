<?php

namespace App\Domain\Exception;

use Exception;
use Throwable;

class CommonException extends Exception
{
    public function __construct(
        Throwable|string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        if ($message instanceof Throwable) {
            $e = $message;
            $message = $e->getMessage();
            $code = $e->getCode();
            $previous = $e;
        }
        parent::__construct($message, $code, $previous);
    }
}
