<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class ChatUserRequestUpdateResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: ChatUserRequestDTO::class))]
    private ChatUserRequestDTO $item;

    public function __construct(string $result, int $timestamp, ChatUserRequestDTO $item)
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

    public function getItem(): ChatUserRequestDTO
    {
        return $this->item;
    }
}
