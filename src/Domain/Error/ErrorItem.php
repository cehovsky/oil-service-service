<?php

namespace App\Domain\Error;

use OpenApi\Attributes as OA;

class ErrorItem implements ErrorItemInterface
{
    #[OA\Property(
        example: 'Name is required field.'
    )]
    private string $message;

    #[OA\Property(example: 'propertyOmitted')]
    private string $code;

    #[OA\Property(
        example: 'name'
    )]
    private ?string $path;

    public function __construct(string $message, string $code, ?string $path)
    {
        $this->message = $message;
        $this->code = $code;
        $this->path = $path;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }
}
