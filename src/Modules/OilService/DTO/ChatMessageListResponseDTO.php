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

    #[OA\Property(example: 'cs-CZ')]
    private string $language;

    /**
     * @var ChatMessageDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: ChatMessageDTO::class)))]
    private array $messages;

    public function __construct(
        string $result,
        int $timestamp,
        string $sessionId,
        string $language,
        array $messages,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->sessionId = $sessionId;
        $this->language = $language;
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

    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @return ChatMessageDTO[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
