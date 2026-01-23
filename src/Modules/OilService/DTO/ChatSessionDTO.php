<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class ChatSessionDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'active')]
    private string $status;

    #[OA\Property(example: 'cs-CZ')]
    private string $language;

    #[OA\Property(example: '2026-01-20T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-01-20T10:05:00+00:00')]
    private string $updatedAt;

    #[OA\Property(example: '2026-01-20T10:10:00+00:00', nullable: true)]
    private ?string $closedAt;

    public function __construct(
        string $id,
        string $status,
        string $language,
        string $createdAt,
        string $updatedAt,
        ?string $closedAt,
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->language = $language;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->closedAt = $closedAt;
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

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function getClosedAt(): ?string
    {
        return $this->closedAt;
    }
}
