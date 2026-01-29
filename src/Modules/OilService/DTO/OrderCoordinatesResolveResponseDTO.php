<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class OrderCoordinatesResolveResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(example: true)]
    private bool $success;

    #[OA\Property(nullable: true, example: 'Address not found.')]
    private ?string $message;

    #[OA\Property(ref: new Model(type: OrderDTO::class))]
    private OrderDTO $order;

    public function __construct(
        string $result,
        int $timestamp,
        bool $success,
        ?string $message,
        OrderDTO $order,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->success = $success;
        $this->message = $message;
        $this->order = $order;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getOrder(): OrderDTO
    {
        return $this->order;
    }
}
