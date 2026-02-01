<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class ChatSessionDetailDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'S2600001')]
    private string $ident;

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

    #[OA\Property(example: 'c4d5e6f7-8901-2345-6789-abcdefabcdef', nullable: true)]
    private ?string $orderId;

    #[OA\Property(example: 'O2600001', nullable: true)]
    private ?string $orderIdent;

    /**
     * @var ChatMessageDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: ChatMessageDTO::class)))]
    private array $messages;

    /**
     * @param ChatMessageDTO[] $messages
     */
    public function __construct(
        string $id,
        string $ident,
        string $status,
        string $language,
        string $createdAt,
        string $updatedAt,
        ?string $closedAt,
        ?string $orderId,
        ?string $orderIdent,
        array $messages,
    ) {
        $this->id = $id;
        $this->ident = $ident;
        $this->status = $status;
        $this->language = $language;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->closedAt = $closedAt;
        $this->orderId = $orderId;
        $this->orderIdent = $orderIdent;
        $this->messages = $messages;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIdent(): string
    {
        return $this->ident;
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

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function getOrderIdent(): ?string
    {
        return $this->orderIdent;
    }

    /**
     * @return ChatMessageDTO[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
