<?php

declare(strict_types=1);

namespace App\Geocoding;

final class ServiceAreaPolygonChecker
{
    public function isPointInsidePolygon(?float $latitude, ?float $longitude, string $polygon): bool
    {
        if ($latitude === null || $longitude === null) {
            return false;
        }

        $points = $this->parsePolygon($polygon);
        if ($points === null) {
            return false;
        }

        if ($points === []) {
            return true;
        }

        if (count($points) < 3) {
            return false;
        }

        $inside = false;
        $j = count($points) - 1;

        for ($i = 0, $pointsCount = count($points); $i < $pointsCount; $i++) {
            [$latI, $lonI] = $points[$i];
            [$latJ, $lonJ] = $points[$j];

            $latitudeDiff = $latJ - $latI;
            if (abs($latitudeDiff) < PHP_FLOAT_EPSILON) {
                $j = $i;
                continue;
            }

            $intersects = (($latI > $latitude) !== ($latJ > $latitude))
                && ($longitude < ($lonJ - $lonI) * ($latitude - $latI) / $latitudeDiff + $lonI);

            if ($intersects) {
                $inside = !$inside;
            }

            $j = $i;
        }

        return $inside;
    }

    /**
     * @return array<int, array{0: float, 1: float}>|null
     */
    private function parsePolygon(string $polygon): ?array
    {
        $trimmedPolygon = trim($polygon);
        if ($trimmedPolygon === '') {
            return [];
        }

        $points = [];
        foreach (explode(';', $trimmedPolygon) as $point) {
            $parts = array_map('trim', explode(',', $point));
            if (count($parts) !== 2 || !is_numeric($parts[0]) || !is_numeric($parts[1])) {
                return null;
            }

            $points[] = [(float) $parts[0], (float) $parts[1]];
        }

        return $points;
    }
}
