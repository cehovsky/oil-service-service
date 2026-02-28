<?php

declare(strict_types=1);

namespace App\Modules\Sepno\DTO;

use App\Modules\Files\DTO\FileDTO;
use App\Modules\Warehouse\DTO\RouteSummaryDTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class SepnoRecordDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(ref: new Model(type: RouteSummaryDTO::class))]
    private RouteSummaryDTO $route;

    #[OA\Property(example: 'accepted')]
    private string $status;

    #[OA\Property(example: 'SEP-1A2B3C4D-20260227104500', nullable: true)]
    private ?string $officialSepnoId;

    #[OA\Property(example: 'car_app')]
    private string $source;

    #[OA\Property(example: 42.5, nullable: true)]
    private ?float $estimatedWasteKg;

    #[OA\Property(example: 48.2, nullable: true)]
    private ?float $actualWasteKg;

    #[OA\Property(ref: new Model(type: FileDTO::class), nullable: true)]
    private ?FileDTO $responseFile;

    #[OA\Property(nullable: true)]
    private ?string $requestXml;

    #[OA\Property(nullable: true)]
    private ?string $responseXml;

    #[OA\Property(nullable: true)]
    private ?string $lastError;

    #[OA\Property(example: '2026-02-27T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-02-27T10:00:05+00:00', nullable: true)]
    private ?string $submittedAt;

    #[OA\Property(example: '2026-02-27T18:45:00+00:00', nullable: true)]
    private ?string $closedAt;

    public function __construct(
        string $id,
        RouteSummaryDTO $route,
        string $status,
        ?string $officialSepnoId,
        string $source,
        ?float $estimatedWasteKg,
        ?float $actualWasteKg,
        ?FileDTO $responseFile,
        ?string $requestXml,
        ?string $responseXml,
        ?string $lastError,
        string $createdAt,
        ?string $submittedAt,
        ?string $closedAt,
    ) {
        $this->id = $id;
        $this->route = $route;
        $this->status = $status;
        $this->officialSepnoId = $officialSepnoId;
        $this->source = $source;
        $this->estimatedWasteKg = $estimatedWasteKg;
        $this->actualWasteKg = $actualWasteKg;
        $this->responseFile = $responseFile;
        $this->requestXml = $requestXml;
        $this->responseXml = $responseXml;
        $this->lastError = $lastError;
        $this->createdAt = $createdAt;
        $this->submittedAt = $submittedAt;
        $this->closedAt = $closedAt;
    }
}
