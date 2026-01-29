<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class OrderCoordinatesUpdateRequestDTO
{
    #[OA\Property(example: 50.087, description: 'Latitude in range -90..90')]
    #[Assert\NotNull]
    #[Assert\Range(min: -90, max: 90)]
    private float $latitude;

    #[OA\Property(example: 14.421, description: 'Longitude in range -180..180')]
    #[Assert\NotNull]
    #[Assert\Range(min: -180, max: 180)]
    private float $longitude;

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }
}
