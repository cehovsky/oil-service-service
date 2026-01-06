<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class OrderListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    private int $pageCount;

    /** @var OrderDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: OrderDTO::class)))]
    private array $orders;

    /**
     * @param OrderDTO[] $orders
     */
    public function __construct(
        string $result,
        int $timestamp,
        array $orders,
        int $pageCount
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->orders = $orders;
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
     * @return OrderDTO[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
