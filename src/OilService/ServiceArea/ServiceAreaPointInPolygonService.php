<?php

declare(strict_types=1);

namespace App\OilService\ServiceArea;

final class ServiceAreaPointInPolygonService
{
    /**
     * @param array<int, array{latitude: float, longitude: float}> $polygon
     */
    public function isInsidePolygon(float $latitude, float $longitude, array $polygon): bool
    {
        $polygonPoints = $polygon;

        if (count($polygonPoints) < 3) {
            return false;
        }

        $firstPoint = $polygonPoints[0];
        $lastPoint = $polygonPoints[count($polygonPoints) - 1];

        if ($firstPoint['latitude'] !== $lastPoint['latitude'] || $firstPoint['longitude'] !== $lastPoint['longitude']) {
            $polygonPoints[] = $firstPoint;
        }

        $inside = false;

        for ($current = 0, $previous = count($polygonPoints) - 1; $current < count($polygonPoints); $previous = $current++) {
            $currentLatitude = $polygonPoints[$current]['latitude'];
            $currentLongitude = $polygonPoints[$current]['longitude'];
            $previousLatitude = $polygonPoints[$previous]['latitude'];
            $previousLongitude = $polygonPoints[$previous]['longitude'];

            $intersects =
                (($currentLatitude > $latitude) !== ($previousLatitude > $latitude))
                && ($longitude < (($previousLongitude - $currentLongitude) * ($latitude - $currentLatitude) / ($previousLatitude - $currentLatitude) + $currentLongitude));

            if ($intersects) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}
