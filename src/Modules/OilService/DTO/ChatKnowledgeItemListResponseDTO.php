<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class ChatKnowledgeItemListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var ChatKnowledgeItemDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: ChatKnowledgeItemDTO::class)))]
    private array $items;

    public function __construct(
        string $result,
        int $timestamp,
        array $items,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->items = $items;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return ChatKnowledgeItemDTO[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
