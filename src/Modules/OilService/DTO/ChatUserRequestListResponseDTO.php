<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class ChatUserRequestListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var ChatUserRequestDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: ChatUserRequestDTO::class)))]
    private array $items;

    public function __construct(string $result, int $timestamp, array $items)
    {
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
     * @return ChatUserRequestDTO[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
