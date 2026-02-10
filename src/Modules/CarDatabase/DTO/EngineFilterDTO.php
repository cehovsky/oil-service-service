<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class EngineFilterDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(ref: new Model(type: EngineSummaryDTO::class))]
    private EngineSummaryDTO $engine;

    #[OA\Property(ref: new Model(type: FilterSummaryDTO::class))]
    private FilterSummaryDTO $filter;

    #[OA\Property(example: true)]
    private bool $isPrimary;

    #[OA\Property(example: 'MANN', nullable: true)]
    private ?string $source;

    #[OA\Property(example: '2026-02-01T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-02-02T10:00:00+00:00')]
    private string $updatedAt;

    public function __construct(
        string $id,
        EngineSummaryDTO $engine,
        FilterSummaryDTO $filter,
        bool $isPrimary,
        ?string $source,
        string $createdAt,
        string $updatedAt,
    ) {
        $this->id = $id;
        $this->engine = $engine;
        $this->filter = $filter;
        $this->isPrimary = $isPrimary;
        $this->source = $source;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEngine(): EngineSummaryDTO
    {
        return $this->engine;
    }

    public function getFilter(): FilterSummaryDTO
    {
        return $this->filter;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
