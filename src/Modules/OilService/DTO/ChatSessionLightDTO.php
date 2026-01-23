<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ChatSessionLightDTO')]
class ChatSessionLightDTO
{
    #[OA\Property(example: 'a1b2c3d4-e5f6-7890-abcd-ef1234567890')]
    private string $id;

    #[OA\Property(example: 'S2600001')]
    private string $ident;

    #[OA\Property(example: 'active')]
    private string $status;

    #[OA\Property(example: 'cs-CZ')]
    private string $language;

    #[OA\Property(example: '2026-01-20T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7', nullable: true)]
    private ?string $orderId;

    #[OA\Property(example: 'O2600001', nullable: true)]
    private ?string $orderIdent;

    public function __construct(
        string $id,
        string $ident,
        string $status,
        string $language,
        string $createdAt,
        ?string $orderId,
        ?string $orderIdent,
    ) {
        $this->id = $id;
        $this->ident = $ident;
        $this->status = $status;
        $this->language = $language;
        $this->createdAt = $createdAt;
        $this->orderId = $orderId;
        $this->orderIdent = $orderIdent;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getIdent(): string
    {
        return $this->ident;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function getOrderIdent(): ?string
    {
        return $this->orderIdent;
    }
}
