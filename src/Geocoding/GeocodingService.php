<?php

declare(strict_types=1);

namespace App\Geocoding;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class GeocodingService
{
    private const string BASE_URL = 'https://nominatim.openstreetmap.org/search';

    private const string USER_AGENT = 'OilServiceGeocoder/1.0';

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function geocodeAddress(string $address): GeocodingResult
    {
        $trimmedAddress = trim($address);

        if ($trimmedAddress === '') {
            return GeocodingResult::failure('Address is empty.');
        }

        try {
            $response = $this->httpClient->request('GET', self::BASE_URL, [
                'query' => [
                    'format' => 'json',
                    'limit' => 1,
                    'q' => $trimmedAddress,
                ],
                'headers' => [
                    'User-Agent' => self::USER_AGENT,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = $response->toArray(false);
        } catch (
            TransportExceptionInterface | ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface $e
        ) {
            return GeocodingResult::failure('Geocoding request failed: ' . $e->getMessage());
        }

        if (!is_array($data) || $data === []) {
            return GeocodingResult::failure('Address not found.');
        }

        $firstItem = $data[0] ?? null;

        if (!is_array($firstItem)) {
            return GeocodingResult::failure('Address not found.');
        }

        $latitude = $firstItem['lat'] ?? null;
        $longitude = $firstItem['lon'] ?? null;

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return GeocodingResult::failure('Invalid coordinates returned by geocoding service.');
        }

        return GeocodingResult::success((float) $latitude, (float) $longitude);
    }
}
