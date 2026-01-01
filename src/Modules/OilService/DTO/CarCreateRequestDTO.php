<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\OilService\DBAL\Enum\CarStatusEnum;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class CarCreateRequestDTO
{
    #[OA\Property(example: 'Ford Transit')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $label;

    #[OA\Property(example: 'CAR001')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    private string $ident;

    #[OA\Property(example: '1A2 3456')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $licensePlate;

    #[OA\Property(example: 'operational')]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: CarStatusEnum::VALUES)]
    private string $status;

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getIdent(): string
    {
        return $this->ident;
    }

    public function setIdent(string $ident): self
    {
        $this->ident = $ident;

        return $this;
    }

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): self
    {
        $this->licensePlate = $licensePlate;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
