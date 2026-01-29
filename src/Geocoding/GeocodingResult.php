<?php

declare(strict_types=1);

namespace App\Geocoding;

final class GeocodingResult
{
    private function __construct(
        private bool $success,
        private ?float $latitude,
        private ?float $longitude,
        private ?string $message,
    ) {
    }

    public static function success(float $latitude, float $longitude): self
    {
        return new self(true, $latitude, $longitude, null);
    }

    public static function failure(string $message): self
    {
        return new self(false, null, null, $message);
    }

    public function isSuccess(): bool
    {
        return $this->success;
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
