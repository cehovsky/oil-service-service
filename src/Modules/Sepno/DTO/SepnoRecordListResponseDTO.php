<?php

declare(strict_types=1);

namespace App\Modules\Sepno\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class SepnoRecordListResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735559999)]
    private int $timestamp;

    /**
     * @var SepnoRecordDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: SepnoRecordDTO::class)))]
    private array $sepnoRecords;

    #[OA\Property(example: 1)]
    private int $pageCount;

    /**
     * @param SepnoRecordDTO[] $sepnoRecords
     */
    public function __construct(string $result, int $timestamp, array $sepnoRecords, int $pageCount)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->sepnoRecords = $sepnoRecords;
        $this->pageCount = $pageCount;
    }
}
