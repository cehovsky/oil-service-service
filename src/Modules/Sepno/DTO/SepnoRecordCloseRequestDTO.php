<?php

declare(strict_types=1);

namespace App\Modules\Sepno\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class SepnoRecordCloseRequestDTO
{
    #[OA\Property(example: 48.2, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?float $actualWasteKg = null;

    public function getActualWasteKg(): ?float
    {
        return $this->actualWasteKg;
    }

    public function setActualWasteKg(?float $actualWasteKg): self
    {
        $this->actualWasteKg = $actualWasteKg;

        return $this;
    }
}
