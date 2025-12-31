<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class AvailableTermListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /** @var AvailableTermDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: AvailableTermDTO::class)))]
    private array $terms;

    /**
     * @param AvailableTermDTO[] $terms
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
     * @return AvailableTermDTO[]
     */
    public function getTerms(): array
    {
        return $this->terms;
    }
}
