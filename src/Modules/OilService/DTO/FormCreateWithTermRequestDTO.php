<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class FormCreateWithTermRequestDTO extends FormCreateRequestDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7', nullable: true)]
    #[Assert\Uuid]
    private ?string $termId = null;

    public function getTermId(): ?string
    {
        return $this->termId;
    }

    public function setTermId(?string $termId): self
    {
        $this->termId = $termId;

        return $this;
    }
}
