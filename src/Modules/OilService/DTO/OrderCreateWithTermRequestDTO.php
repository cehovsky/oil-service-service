<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Modules\OilService\Validation\Constraint\ExistingRoute;
use App\Modules\OilService\Validation\Constraint\ExistingOilServiceUser;
use App\Modules\OilService\Validation\Constraint\ExistingCustomerCar;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class OrderCreateWithTermRequestDTO extends OrderCreateRequestDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ExistingOilServiceUser]
    private string $userId;

    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7', nullable: true)]
    #[Assert\Uuid]
    #[ExistingRoute]
    private ?string $routeId = null;

    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7', nullable: true)]
    #[Assert\Uuid]
    #[ExistingCustomerCar]
    private ?string $customerCarId = null;

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getRouteId(): ?string
    {
        return $this->routeId;
    }

    public function setRouteId(?string $routeId): self
    {
        $this->routeId = $routeId;

        return $this;
    }

    public function getCustomerCarId(): ?string
    {
        return $this->customerCarId;
    }

    public function setCustomerCarId(?string $customerCarId): self
    {
        $this->customerCarId = $customerCarId;

        return $this;
    }
}
