<?php

declare(strict_types=1);

namespace App\VehicleDataCube;

final readonly class VehicleDataCubeService
{
    public function __construct(
        private VehicleDataCubeClient $client,
    ) {
    }

    /**
     * @return array<string, mixed>|null
     */
    public function fetchVehicleDataByVin(string $vin): ?array
    {
        $response = $this->client->fetchByVin($vin);

        if ($response->getStatus() !== 1) {
            return null;
        }

        return $response->getData();
    }
}
