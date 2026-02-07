<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Modules\OilService\Validation\Constraint\ExistingOilServiceUser;
use App\Modules\OilService\Validation\Constraint\UniqueCustomerCarVin;
use App\OilService\DBAL\Enum\CustomerCarBrandEnum;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueCustomerCarVin]
class CustomerCarCreateRequestDTO
{
    #[OA\Property(example: '1A2 3456')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $licensePlate;

    #[OA\Property(enum: CustomerCarBrandEnum::VALUES, nullable: true, example: 'skoda')]
    #[Assert\Choice(callback: [CustomerCarBrandEnum::class, 'values'])]
    private ?string $brand = null;

    #[OA\Property(example: 'Octavia', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $model = null;

    #[OA\Property(example: 'TMBEFF654V7529422', nullable: true)]
    #[Assert\Length(max: 17)]
    private ?string $vin = null;

    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7', nullable: true)]
    #[Assert\Uuid]
    #[ExistingOilServiceUser]
    private ?string $userId = null;

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): self
    {
        $this->licensePlate = $licensePlate;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function setVin(?string $vin): self
    {
        $this->vin = $vin;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }
}
