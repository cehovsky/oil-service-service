<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Modules\OilService\Validation\Constraint\ExistingRoute;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class FormCreateWithTermRequestDTO extends FormCreateRequestDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7', nullable: true)]
    #[Assert\Uuid]
    #[ExistingRoute]
    private ?string $routeId = null;

    public function getRouteId(): ?string
    {
        return $this->routeId;
    }

    public function setRouteId(?string $routeId): self
    {
        $this->routeId = $routeId;

        return $this;
    }
}
