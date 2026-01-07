<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class RecyclingListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var RecyclingDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: RecyclingDTO::class)))]
    private array $recyclings;

    private int $pageCount;

    /**
     * @param RecyclingDTO[] $recyclings
     */
    public function __construct(string $result, int $timestamp, array $recyclings, int $pageCount)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->recyclings = $recyclings;
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
     * @return RecyclingDTO[]
     */
    public function getRecyclings(): array
    {
        return $this->recyclings;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
