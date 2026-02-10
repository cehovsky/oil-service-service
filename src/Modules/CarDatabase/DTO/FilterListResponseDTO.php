<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class FilterListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    private int $pageCount;

    /** @var FilterDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: FilterDTO::class)))]
    private array $filters;

    /**
     * @param FilterDTO[] $filters
     */
    public function __construct(string $result, int $timestamp, array $filters, int $pageCount)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->filters = $filters;
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
     * @return FilterDTO[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
