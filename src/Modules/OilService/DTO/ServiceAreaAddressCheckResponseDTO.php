<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class ServiceAreaAddressCheckResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    private int $timestamp;

    #[OA\Property(example: true)]
    private bool $isRecognized;

    #[OA\Property(example: true, nullable: true)]
    private ?bool $isWithinServiceArea;

    #[OA\Property(example: 50.087, nullable: true)]
    private ?float $latitude;

    #[OA\Property(example: 14.421, nullable: true)]
    private ?float $longitude;

    #[OA\Property(example: null, nullable: true)]
    private ?string $message;

    public function __construct(
        string $result,
        int $timestamp,
        bool $isRecognized,
        ?bool $isWithinServiceArea,
        ?float $latitude,
        ?float $longitude,
        ?string $message,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->isRecognized = $isRecognized;
        $this->isWithinServiceArea = $isWithinServiceArea;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->message = $message;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function isRecognized(): bool
    {
        return $this->isRecognized;
    }

    public function getIsWithinServiceArea(): ?bool
    {
        return $this->isWithinServiceArea;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
