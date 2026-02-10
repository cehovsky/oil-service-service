<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class EngineFilterCreateRequestDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    #[Assert\Uuid]
    #[Assert\NotBlank]
    private string $engineId;

    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    #[Assert\Uuid]
    #[Assert\NotBlank]
    private string $filterId;

    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isPrimary;

    #[OA\Property(example: 'MANN', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $source = null;

    public function getEngineId(): string
    {
        return $this->engineId;
    }

    public function setEngineId(string $engineId): self
    {
        $this->engineId = $engineId;

        return $this;
    }

    public function getFilterId(): string
    {
        return $this->filterId;
    }

    public function setFilterId(string $filterId): self
    {
        $this->filterId = $filterId;

        return $this;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): self
    {
        $this->isPrimary = $isPrimary;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }
}
