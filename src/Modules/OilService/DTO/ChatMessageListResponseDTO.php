<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class ChatMessageListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $sessionId;

    #[OA\Property(example: 'S2600001')]
    private string $sessionIdent;

    #[OA\Property(example: 'active')]
    private string $sessionStatus;

    #[OA\Property(example: 'cs-CZ')]
    private string $language;

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
        string $result,
        int $timestamp,
        string $sessionId,
        string $sessionIdent,
        string $sessionStatus,
        string $language,
        ?string $orderId,
        ?string $orderIdent,
        array $messages,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->sessionId = $sessionId;
        $this->sessionIdent = $sessionIdent;
        $this->sessionStatus = $sessionStatus;
        $this->language = $language;
        $this->orderId = $orderId;
        $this->orderIdent = $orderIdent;
        $this->messages = $messages;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    public function getSessionIdent(): string
    {
        return $this->sessionIdent;
    }

    public function getSessionStatus(): string
    {
        return $this->sessionStatus;
    }

    public function getLanguage(): string
    {
        return $this->language;
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
