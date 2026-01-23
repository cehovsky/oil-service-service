<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class ChatUserRequestDetailDTO
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

    #[OA\Property(example: false)]
    private bool $isResolved;

    #[OA\Property(example: 'Call customer after 3pm', nullable: true)]
    private ?string $note;

    #[OA\Property(ref: new OA\Schema(ref: '#/components/schemas/ChatSessionDetailDTO'), nullable: true)]
    private ?ChatSessionDetailDTO $session;

    public function __construct(
        string $id,
        string $status,
        string $content,
        string $createdAt,
        ?string $resolvedAt,
        bool $isResolved,
        ?string $note,
        ?ChatSessionDetailDTO $session,
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->resolvedAt = $resolvedAt;
        $this->isResolved = $isResolved;
        $this->note = $note;
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

    public function getIsResolved(): bool
    {
        return $this->isResolved;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getSession(): ?ChatSessionDetailDTO
    {
        return $this->session;
    }
}
