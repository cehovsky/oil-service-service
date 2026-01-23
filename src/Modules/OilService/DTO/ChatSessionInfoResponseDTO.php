<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class ChatSessionInfoResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: ChatSessionDetailDTO::class))]
    private ChatSessionDetailDTO $item;

    public function __construct(string $result, int $timestamp, ChatSessionDetailDTO $item)
    {
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

    public function getItem(): ChatSessionDetailDTO
    {
        return $this->item;
    }
}
