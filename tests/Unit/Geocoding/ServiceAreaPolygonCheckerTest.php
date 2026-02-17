<?php

declare(strict_types=1);

namespace App\Tests\Unit\Geocoding;

use App\Geocoding\ServiceAreaPolygonChecker;
use PHPUnit\Framework\TestCase;

class ServiceAreaPolygonCheckerTest extends TestCase
{
    private ServiceAreaPolygonChecker $checker;

    protected function setUp(): void
    {
        $this->checker = new ServiceAreaPolygonChecker();
    }

    public function testReturnsTrueForEmptyPolygonConfiguration(): void
    {
        $this->assertTrue($this->checker->isPointInsidePolygon(50.087, 14.421, ''));
    }

    public function testReturnsTrueForPointInsidePolygon(): void
    {
        $polygon = '50.0000,14.0000;50.0000,15.0000;51.0000,15.0000;51.0000,14.0000';

        $this->assertTrue($this->checker->isPointInsidePolygon(50.5, 14.5, $polygon));
    }

    public function testReturnsFalseForPointOutsidePolygon(): void
    {
        $polygon = '50.0000,14.0000;50.0000,15.0000;51.0000,15.0000;51.0000,14.0000';

        $this->assertFalse($this->checker->isPointInsidePolygon(52.0, 14.5, $polygon));
    }
}
