<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class ChatMessageDTO
{
    #[OA\Property(example: 'assistant')]
    private string $role;

    #[OA\Property(example: 'Dobrý den, rád vám pomohu s výměnou oleje.')]
    private string $content;

    #[OA\Property(example: '2026-01-20T10:00:00+00:00')]
    private string $createdAt;

    public function __construct(
        string $role,
        string $content,
        string $createdAt,
    ) {
        $this->role = $role;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
