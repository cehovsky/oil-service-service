<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class PublicOrderReportResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: PublicOrderReportDTO::class))]
    private PublicOrderReportDTO $report;

    public function __construct(string $result, int $timestamp, PublicOrderReportDTO $report)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->report = $report;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getReport(): PublicOrderReportDTO
    {
        return $this->report;
    }
}
