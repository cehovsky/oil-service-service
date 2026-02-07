<?php

declare(strict_types=1);

namespace App\VehicleDataCube;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class VehicleDataCubeClient
{
    private const string BASE_URL = 'https://api.dataovozidlech.cz/api/vehicletechnicaldata/v2';

    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire('%env(VEHICLE_DATA_CUBE_API_KEY)%')] private string $apiKey,
    ) {
    }

    public function fetchByVin(string $vin): VehicleDataCubeResponse
    {
        try {
            $response = $this->httpClient->request('GET', self::BASE_URL, [
                'query' => [
                    'vin' => $vin,
                ],
                'headers' => [
                    'API_KEY' => $this->apiKey,
                ],
            ]);

            $responseData = $response->toArray(false);

            $status = (int)($responseData['Status'] ?? 0);
            $data = $responseData['Data'] ?? null;

            return new VehicleDataCubeResponse(
                $status,
                is_array($data) ? $data : null,
                $responseData['Message'] ?? null,
            );
        } catch (TransportExceptionInterface $exception) {
            return new VehicleDataCubeResponse(0, null, $exception->getMessage());
        }
    }
}
