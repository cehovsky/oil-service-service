<?php

declare(strict_types=1);

namespace App\Modules\Sepno\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class SepnoRecordCreateRequestDTO
{
    #[OA\Property(example: 42.5, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?float $estimatedWasteKg = null;

    public function getEstimatedWasteKg(): ?float
    {
        return $this->estimatedWasteKg;
    }

    public function setEstimatedWasteKg(?float $estimatedWasteKg): self
    {
        $this->estimatedWasteKg = $estimatedWasteKg;

        return $this;
    }
}
