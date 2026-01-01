<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class RouteUpdateRequestDTO
{
    #[OA\Property(example: 'a1b2c3d4-e5f6-7890-abcd-ef1234567890', nullable: true)]
    #[Assert\Uuid]
    private ?string $carId = null;

    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isActive;

    #[OA\Property(example: '2025-01-15', description: 'Date in format YYYY-MM-DD')]
    #[Assert\NotBlank]
    #[Assert\Date]
    private string $date;

    /**
     * @var string[]|null
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string', example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7'), nullable: true)]
    #[Assert\All([
        new Assert\Uuid(),
    ])]
    private ?array $termIds = null;

    public function getCarId(): ?string
    {
        return $this->carId;
    }

    public function setCarId(?string $carId): self
    {
        $this->carId = $carId;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getTermIds(): ?array
    {
        return $this->termIds;
    }

    /**
     * @param string[]|null $termIds
     */
    public function setTermIds(?array $termIds): self
    {
        $this->termIds = $termIds;

        return $this;
    }
}
