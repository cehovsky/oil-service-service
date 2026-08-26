<?php

declare(strict_types=1);

namespace App\Tests\Unit\OilService\ServiceArea;

use App\OilService\ServiceArea\ServiceAreaPointInPolygonService;
use App\OilService\ServiceArea\ServiceAreaPolygonProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ServiceAreaPointInPolygonServiceTest extends TestCase
{
    private ServiceAreaPointInPolygonService $service;

    protected function setUp(): void
    {
        $this->service = new ServiceAreaPointInPolygonService();
    }

    /**
     * @return array<int, array{latitude: float, longitude: float}>
     */
    private static function square(): array
    {
        return [
            ['latitude' => 0.0, 'longitude' => 0.0],
            ['latitude' => 0.0, 'longitude' => 10.0],
            ['latitude' => 10.0, 'longitude' => 10.0],
            ['latitude' => 10.0, 'longitude' => 0.0],
        ];
    }

    public function testPointInsideOpenSquareIsDetected(): void
    {
        self::assertTrue($this->service->isInsidePolygon(5.0, 5.0, self::square()));
    }

    public function testExplicitlyClosedPolygonBehavesSameAsOpenOne(): void
    {
        $closed = self::square();
        $closed[] = ['latitude' => 0.0, 'longitude' => 0.0];

        self::assertTrue($this->service->isInsidePolygon(5.0, 5.0, $closed));
        self::assertFalse($this->service->isInsidePolygon(50.0, 50.0, $closed));
    }

    #[DataProvider('provideOutsidePoints')]
    public function testPointOutsideSquareIsRejected(float $latitude, float $longitude): void
    {
        self::assertFalse($this->service->isInsidePolygon($latitude, $longitude, self::square()));
    }

    /**
     * @return iterable<string, array{0: float, 1: float}>
     */
    public static function provideOutsidePoints(): iterable
    {
        yield 'north' => [15.0, 5.0];
        yield 'south' => [-1.0, 5.0];
        yield 'east' => [5.0, 15.0];
        yield 'west' => [5.0, -0.5];
    }

    #[DataProvider('provideDegeneratePolygons')]
    public function testPolygonWithLessThanThreePointsIsNeverMatched(array $polygon): void
    {
        self::assertFalse($this->service->isInsidePolygon(5.0, 5.0, $polygon));
    }

    /**
     * @return iterable<string, array{0: array<int, array{latitude: float, longitude: float}>}>
     */
    public static function provideDegeneratePolygons(): iterable
    {
        yield 'empty' => [[]];
        yield 'single point' => [[['latitude' => 5.0, 'longitude' => 5.0]]];
        yield 'line segment' => [[
            ['latitude' => 0.0, 'longitude' => 0.0],
            ['latitude' => 10.0, 'longitude' => 10.0],
        ]];
    }

    public function testConcavePolygonExcludesTheNotch(): void
    {
        // "U" shape - the gap between both arms must not be reported as inside.
        $uShape = [
            ['latitude' => 0.0, 'longitude' => 0.0],
            ['latitude' => 0.0, 'longitude' => 10.0],
            ['latitude' => 10.0, 'longitude' => 10.0],
            ['latitude' => 10.0, 'longitude' => 8.0],
            ['latitude' => 2.0, 'longitude' => 8.0],
            ['latitude' => 2.0, 'longitude' => 2.0],
            ['latitude' => 10.0, 'longitude' => 2.0],
            ['latitude' => 10.0, 'longitude' => 0.0],
        ];

        self::assertTrue($this->service->isInsidePolygon(1.0, 5.0, $uShape));
        self::assertTrue($this->service->isInsidePolygon(5.0, 9.0, $uShape));
        self::assertFalse($this->service->isInsidePolygon(5.0, 5.0, $uShape));
    }

    public function testRealServiceAreaCoversPragueButNotBrno(): void
    {
        $polygon = (new ServiceAreaPolygonProvider())->getPolygonCoordinates();

        self::assertGreaterThan(3, count($polygon));
        self::assertTrue($this->service->isInsidePolygon(50.0874, 14.4213, $polygon), 'Prague centre must be served.');
        self::assertTrue($this->service->isInsidePolygon(50.0451, 14.3183, $polygon), 'Prague 5 must be served.');
        self::assertFalse($this->service->isInsidePolygon(49.1951, 16.6068, $polygon), 'Brno must not be served.');
        self::assertFalse($this->service->isInsidePolygon(49.8209, 18.2625, $polygon), 'Ostrava must not be served.');
    }
}
