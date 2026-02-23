<?php

declare(strict_types=1);

namespace App\OilService\ServiceArea;

use App\Geocoding\GeocodingService;

final class ServiceAreaAddressEvaluationService
{
    /**
     * @var array<string, ServiceAreaAddressEvaluationResult>
     */
    private array $cache = [];

    public function __construct(
        private readonly GeocodingService $geocodingService,
        private readonly ServiceAreaPolygonProvider $polygonProvider,
        private readonly ServiceAreaPointInPolygonService $pointInPolygonService,
    ) {
    }

    public function evaluateAddress(string $address): ServiceAreaAddressEvaluationResult
    {
        $normalizedAddress = trim($address);

        if ($normalizedAddress === '') {
            return ServiceAreaAddressEvaluationResult::unrecognized('Address is empty.');
        }

        $cacheKey = mb_strtolower($normalizedAddress);
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $geocodingResult = $this->geocodingService->geocodeAddress($normalizedAddress);

        if (!$geocodingResult->isSuccess() || $geocodingResult->getLatitude() === null || $geocodingResult->getLongitude() === null) {
            $result = ServiceAreaAddressEvaluationResult::unrecognized($geocodingResult->getMessage());
            $this->cache[$cacheKey] = $result;

            return $result;
        }

        $latitude = $geocodingResult->getLatitude();
        $longitude = $geocodingResult->getLongitude();

        $isWithinServiceArea = $this->pointInPolygonService->isInsidePolygon(
            $latitude,
            $longitude,
            $this->polygonProvider->getPolygonCoordinates(),
        );

        $result = ServiceAreaAddressEvaluationResult::recognized($latitude, $longitude, $isWithinServiceArea);
        $this->cache[$cacheKey] = $result;

        return $result;
    }

    public function evaluateCoordinates(float $latitude, float $longitude): bool
    {
        return $this->pointInPolygonService->isInsidePolygon(
            $latitude,
            $longitude,
            $this->polygonProvider->getPolygonCoordinates(),
        );
    }
}
