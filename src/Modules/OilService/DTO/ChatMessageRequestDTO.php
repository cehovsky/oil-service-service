<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class ChatMessageRequestDTO
{
    #[OA\Property(example: 'Dobrý den, potřebuji vyměnit olej na Octavii 1.6 TDI.')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 4000)]
    private string $message;

    #[OA\Property(example: 'cs-CZ', nullable: true)]
    #[Assert\Length(max: 10)]
    #[Assert\Regex(pattern: '/^[a-z]{2}(?:-[A-Z]{2})?$/')]
    private ?string $language = null;

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }
}
