<?php

declare(strict_types=1);

namespace App\Tests\Unit\OilService\ServiceArea;

use App\OilService\ServiceArea\ServiceAreaAddressEvaluationResult;
use PHPUnit\Framework\TestCase;

final class ServiceAreaAddressEvaluationResultTest extends TestCase
{
    public function testRecognizedAddressInsideServiceArea(): void
    {
        $result = ServiceAreaAddressEvaluationResult::recognized(50.0874, 14.4213, true);

        self::assertTrue($result->isRecognized());
        self::assertSame(50.0874, $result->getLatitude());
        self::assertSame(14.4213, $result->getLongitude());
        self::assertTrue($result->getWithinServiceArea());
        self::assertNull($result->getMessage());
    }

    public function testRecognizedAddressOutsideServiceArea(): void
    {
        $result = ServiceAreaAddressEvaluationResult::recognized(49.1951, 16.6068, false);

        self::assertTrue($result->isRecognized());
        self::assertFalse($result->getWithinServiceArea());
        self::assertNull($result->getMessage());
    }

    public function testUnrecognizedAddressCarriesMessageAndNoCoordinates(): void
    {
        $result = ServiceAreaAddressEvaluationResult::unrecognized('Address could not be geocoded.');

        self::assertFalse($result->isRecognized());
        self::assertNull($result->getLatitude());
        self::assertNull($result->getLongitude());
        self::assertNull($result->getWithinServiceArea());
        self::assertSame('Address could not be geocoded.', $result->getMessage());
    }

    public function testUnrecognizedAddressWithoutMessage(): void
    {
        $result = ServiceAreaAddressEvaluationResult::unrecognized(null);

        self::assertFalse($result->isRecognized());
        self::assertNull($result->getMessage());
    }
}
