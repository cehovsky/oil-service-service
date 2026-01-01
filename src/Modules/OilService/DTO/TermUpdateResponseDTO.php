<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class TermUpdateResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735559999)]
    private int $timestamp;

    #[OA\Property(ref: new Model(type: TermDTO::class))]
    private TermDTO $term;

    public function __construct(
        string $result,
        int $timestamp,
        TermDTO $term,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->term = $term;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getTerm(): TermDTO
    {
        return $this->term;
    }
}
