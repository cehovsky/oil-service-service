<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class ChatUserRequestDTO
{
    #[OA\Property(example: 'a1b2c3d4-e5f6-7890-abcd-ef1234567890')]
    private string $id;

    #[OA\Property(example: 'open')]
    private string $status;

    #[OA\Property(example: 'Potřebuji, abyste mi zavolali zpět ohledně termínu.')]
    private string $content;

    #[OA\Property(example: '2026-01-20T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-01-21T10:00:00+00:00', nullable: true)]
    private ?string $resolvedAt;

    #[OA\Property(ref: new OA\Schema(ref: '#/components/schemas/ChatSessionLightDTO'))]
    private ChatSessionLightDTO $session;

    public function __construct(
        string $id,
        string $status,
        string $content,
        string $createdAt,
        ?string $resolvedAt,
        ChatSessionLightDTO $session,
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->resolvedAt = $resolvedAt;
        $this->session = $session;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getResolvedAt(): ?string
    {
        return $this->resolvedAt;
    }

    public function getSession(): ChatSessionLightDTO
    {
        return $this->session;
    }
}
