<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class RouteDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'a1b2c3d4-e5f6-7890-abcd-ef1234567890', nullable: true)]
    private ?string $carId;

    #[OA\Property(example: 'Vozidlo A (AB-123-CD)', nullable: true)]
    private ?string $carLabel;

    #[OA\Property(example: true)]
    private bool $isActive;

    #[OA\Property(example: '2025-01-15')]
    private string $date;

    #[OA\Property(example: '2025-12-30T10:00:00+00:00')]
    private string $createdAt;

    /**
     * @var string[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string', example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7'))]
    private array $termIds;

    /**
     * @param string[] $termIds
     */
    public function __construct(
        string $id,
        ?string $carId,
        ?string $carLabel,
        bool $isActive,
        string $date,
        string $createdAt,
        array $termIds,
    ) {
        $this->id = $id;
        $this->carId = $carId;
        $this->carLabel = $carLabel;
        $this->isActive = $isActive;
        $this->date = $date;
        $this->createdAt = $createdAt;
        $this->termIds = $termIds;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCarId(): ?string
    {
        return $this->carId;
    }

    public function getCarLabel(): ?string
    {
        return $this->carLabel;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @return string[]
     */
    public function getTermIds(): array
    {
        return $this->termIds;
    }
}
