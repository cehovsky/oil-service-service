<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class ChatSessionLightDTO
{
    #[OA\Property(example: 'a1b2c3d4-e5f6-7890-abcd-ef1234567890')]
    private string $id;

    #[OA\Property(example: 'active')]
    private string $status;

    #[OA\Property(example: 'cs-CZ')]
    private string $language;

    #[OA\Property(example: '2026-01-20T10:00:00+00:00')]
    private string $createdAt;

    public function __construct(
        string $id,
        string $status,
        string $language,
        string $createdAt,
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->language = $language;
        $this->createdAt = $createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
