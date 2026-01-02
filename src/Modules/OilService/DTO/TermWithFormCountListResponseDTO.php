<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class TermWithFormCountListResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735559999)]
    private int $timestamp;

    /** @var TermWithFormCountDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: TermWithFormCountDTO::class)))]
    private array $terms;

    /**
     * @param TermWithFormCountDTO[] $terms
     */
    public function __construct(string $result, int $timestamp, array $terms)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->terms = $terms;
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
     * @return TermWithFormCountDTO[]
     */
    public function getTerms(): array
    {
        return $this->terms;
    }
}
