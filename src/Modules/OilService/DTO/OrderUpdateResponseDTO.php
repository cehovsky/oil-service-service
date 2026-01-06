<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class OrderUpdateResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: OrderDTO::class))]
    private OrderDTO $order;

    public function __construct(
        string $result,
        int $timestamp,
        OrderDTO $order
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
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

    public function getOrder(): OrderDTO
    {
        return $this->order;
    }
}
