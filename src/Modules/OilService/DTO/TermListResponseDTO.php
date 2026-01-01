<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class TermListResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735559999)]
    private int $timestamp;

    /** @var TermDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: '#/components/schemas/TermDTO'))]
    private array $terms;

    #[OA\Property(example: 10)]
    private int $pageCount;

    /**
     * @param TermDTO[] $terms
     */
    public function __construct(
        string $result,
        int $timestamp,
        array $terms,
        int $pageCount,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->terms = $terms;
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
     * @return TermDTO[]
     */
    public function getTerms(): array
    {
        return $this->terms;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
