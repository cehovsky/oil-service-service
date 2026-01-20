<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class ChatKnowledgeItemUpdateResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: ChatKnowledgeItemDTO::class))]
    private ChatKnowledgeItemDTO $item;

    public function __construct(
        string $result,
        int $timestamp,
        ChatKnowledgeItemDTO $item,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->item = $item;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getItem(): ChatKnowledgeItemDTO
    {
        return $this->item;
    }
}
