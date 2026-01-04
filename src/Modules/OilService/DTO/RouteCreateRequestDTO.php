<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Modules\OilService\Validation\Constraint\ExistingCar;
use App\Modules\OilService\Validation\Constraint\ExistingTermIds;
use App\Modules\OilService\Validation\Constraint\ExistingStorageContainerIds;
use App\Modules\OilService\Validation\Constraint\ExistingAuthUserIds;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class RouteCreateRequestDTO
{
    #[OA\Property(example: 'a1b2c3d4-e5f6-7890-abcd-ef1234567890', nullable: true)]
    #[Assert\Uuid]
    #[ExistingCar]
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
    #[ExistingTermIds]
    private ?array $termIds = null;

    /**
     * @var string[]|null
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string', example: 'c2a63a4d-46b4-4d3b-98e3-d7343776e0b1'), nullable: true)]
    #[Assert\All([
        new Assert\Uuid(),
    ])]
    #[ExistingStorageContainerIds]
    private ?array $storageContainerIds = null;

    /**
     * @var string[]|null
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string', example: 'd0e2f84c-3ef8-4c4d-a7c6-6e1c1e1f4b5c'), nullable: true)]
    #[Assert\All([
        new Assert\Uuid(),
    ])]
    #[ExistingAuthUserIds]
    private ?array $userIds = null;

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

    /**
     * @return string[]|null
     */
    public function getStorageContainerIds(): ?array
    {
        return $this->storageContainerIds;
    }

    /**
     * @param string[]|null $storageContainerIds
     */
    public function setStorageContainerIds(?array $storageContainerIds): self
    {
        $this->storageContainerIds = $storageContainerIds;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getUserIds(): ?array
    {
        return $this->userIds;
    }

    /**
     * @param string[]|null $userIds
     */
    public function setUserIds(?array $userIds): self
    {
        $this->userIds = $userIds;

        return $this;
    }
}
