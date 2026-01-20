<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use OpenApi\Attributes as OA;

class ChatDefaultMessageResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(example: 'cs-CZ')]
    private string $language;

    #[OA\Property(example: 'Dobrý den, mohu vám pomoci s výměnou oleje?')]
    private string $message;

    public function __construct(
        string $result,
        int $timestamp,
        string $language,
        string $message,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->language = $language;
        $this->message = $message;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
