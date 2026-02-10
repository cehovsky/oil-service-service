<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class EngineFilterListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    private int $pageCount;

    /** @var EngineFilterDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: EngineFilterDTO::class)))]
    private array $engineFilters;

    /**
     * @param EngineFilterDTO[] $engineFilters
     */
    public function __construct(string $result, int $timestamp, array $engineFilters, int $pageCount)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->engineFilters = $engineFilters;
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
     * @return EngineFilterDTO[]
     */
    public function getEngineFilters(): array
    {
        return $this->engineFilters;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
