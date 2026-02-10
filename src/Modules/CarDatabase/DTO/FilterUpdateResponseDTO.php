<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class FilterUpdateResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: FilterDTO::class))]
    private FilterDTO $filter;

    public function __construct(string $result, int $timestamp, FilterDTO $filter)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->filter = $filter;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getFilter(): FilterDTO
    {
        return $this->filter;
    }
}
