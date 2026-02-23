<?php

declare(strict_types=1);

namespace App\OilService\ServiceArea;

final class ServiceAreaAddressEvaluationResult
{
    private function __construct(
        private bool $recognized,
        private ?float $latitude,
        private ?float $longitude,
        private ?bool $withinServiceArea,
        private ?string $message,
    ) {
    }

    public static function recognized(float $latitude, float $longitude, bool $withinServiceArea): self
    {
        return new self(true, $latitude, $longitude, $withinServiceArea, null);
    }

    public static function unrecognized(?string $message): self
    {
        return new self(false, null, null, null, $message);
    }

    public function isRecognized(): bool
    {
        return $this->recognized;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getWithinServiceArea(): ?bool
    {
        return $this->withinServiceArea;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
