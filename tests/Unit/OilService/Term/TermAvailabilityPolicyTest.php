<?php

declare(strict_types=1);

namespace App\Tests\Unit\OilService\Term;

use App\OilService\Term\TermAvailabilityPolicy;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class TermAvailabilityPolicyTest extends TestCase
{
    public function testMinimumAvailableDateIsTwoDaysAhead(): void
    {
        $policy = new TermAvailabilityPolicy();
        $expected = (new DateTimeImmutable('today'))->modify('+2 days');

        $minimum = $policy->getMinimumAvailableDate();

        self::assertSame(2, TermAvailabilityPolicy::MIN_DAYS_AHEAD);
        self::assertSame($expected->format('Y-m-d'), $minimum->format('Y-m-d'));
    }

    public function testMinimumAvailableDateIsNormalizedToMidnight(): void
    {
        $minimum = (new TermAvailabilityPolicy())->getMinimumAvailableDate();

        self::assertSame('00:00:00', $minimum->format('H:i:s'));
    }

    public function testTodayAndTomorrowAreNotBookable(): void
    {
        $minimum = (new TermAvailabilityPolicy())->getMinimumAvailableDate();

        self::assertGreaterThan(new DateTimeImmutable('tomorrow'), $minimum);
        self::assertGreaterThan(new DateTimeImmutable('today'), $minimum);
    }

    public function testPolicyIsStableWithinSingleDay(): void
    {
        $policy = new TermAvailabilityPolicy();

        self::assertEquals($policy->getMinimumAvailableDate(), $policy->getMinimumAvailableDate());
    }
}
