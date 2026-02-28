<?php

declare(strict_types=1);

namespace App\Modules\Sepno\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class SepnoRecordInfoResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735559999)]
    private int $timestamp;

    #[OA\Property(ref: new Model(type: SepnoRecordDTO::class))]
    private SepnoRecordDTO $sepnoRecord;

    public function __construct(string $result, int $timestamp, SepnoRecordDTO $sepnoRecord)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->sepnoRecord = $sepnoRecord;
    }
}
