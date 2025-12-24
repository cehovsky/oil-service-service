<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Symfony\Component\HttpClient\Exception\ClientException;
use Throwable;

class BadRequestException extends CommonException
{
    public function __construct(
        Throwable | string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        private readonly ?string $content = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function createFromString(string $message): self
    {
        return new self($message);
    }

    public static function createFromException(string $message, Throwable $e): self
    {
        return new self($message . ": {$e->getMessage()}", $e->getCode(), $e);
    }

    public static function createFromClientException(string $message, ClientException $e): self
    {
        try {
            return new self(
                $message . ": {$e->getMessage()}",
                $e->getCode(),
                $e,
                $e->getResponse()->getContent(false),
            );
        } catch (Throwable $e) {
            return self::createFromException($message, $e);
        }
    }

    public function getContent(): ?string
    {
        return $this->content;
    }
}
