<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class ChatSessionListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var ChatSessionDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: ChatSessionDTO::class)))]
    private array $items;

    private int $pageCount;

    /**
     * @param ChatSessionDTO[] $items
     */
    public function __construct(string $result, int $timestamp, array $items, int $pageCount)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->items = $items;
        $this->pageCount = $pageCount;
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
     * @return ChatSessionDTO[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
