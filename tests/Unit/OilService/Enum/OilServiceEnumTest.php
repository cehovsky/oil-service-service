<?php

declare(strict_types=1);

namespace App\Tests\Unit\OilService\Enum;

use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use PHPUnit\Framework\TestCase;
use ValueError;

final class OilServiceEnumTest extends TestCase
{
    public function testOrderStatusValuesMatchTheConstant(): void
    {
        self::assertSame(OrderStatusEnum::VALUES, OrderStatusEnum::values());
        self::assertCount(5, OrderStatusEnum::cases());
        self::assertSame('new', OrderStatusEnum::NEW->value);
        self::assertSame('in_preparation', OrderStatusEnum::IN_PREPARATION->value);
        self::assertSame('canceled', OrderStatusEnum::CANCELED->value);
    }

    public function testOrderStatusCanBeCreatedFromValue(): void
    {
        self::assertSame(OrderStatusEnum::COMPLETED, OrderStatusEnum::from('completed'));
        self::assertNull(OrderStatusEnum::tryFrom('COMPLETED'));
        self::assertNull(OrderStatusEnum::tryFrom('unknown'));
    }

    public function testOrderStatusFromRejectsUnknownValue(): void
    {
        $this->expectException(ValueError::class);

        OrderStatusEnum::from('done');
    }

    public function testRealizationTimeSlotValuesMatchTheConstant(): void
    {
        self::assertSame(RealizationTimeSlotEnum::VALUES, RealizationTimeSlotEnum::values());
        self::assertCount(3, RealizationTimeSlotEnum::cases());
        self::assertSame(
            ['morning', 'lunch', 'afternoon'],
            RealizationTimeSlotEnum::values(),
        );
    }

    public function testRealizationTimeSlotCanBeCreatedFromValue(): void
    {
        self::assertSame(RealizationTimeSlotEnum::MORNING, RealizationTimeSlotEnum::from('morning'));
        self::assertSame(RealizationTimeSlotEnum::AFTERNOON, RealizationTimeSlotEnum::tryFrom('afternoon'));
        self::assertNull(RealizationTimeSlotEnum::tryFrom('evening'));
    }
}
