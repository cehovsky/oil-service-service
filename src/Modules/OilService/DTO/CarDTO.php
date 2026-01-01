<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class CarDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'Ford Transit')]
    private string $label;

    #[OA\Property(example: 'CAR001')]
    private string $ident;

    #[OA\Property(example: '1A2 3456')]
    private string $licensePlate;

    #[OA\Property(example: 'operational')]
    private string $status;

    #[OA\Property(example: '2025-12-30T10:00:00+00:00')]
    private string $createdAt;

    public function __construct(
        string $id,
        string $label,
        string $ident,
        string $licensePlate,
        string $status,
        string $createdAt,
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->ident = $ident;
        $this->licensePlate = $licensePlate;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getIdent(): string
    {
        return $this->ident;
    }

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
