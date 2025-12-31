<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use OpenApi\Attributes as OA;

class AvailableTermDTO
{
    #[OA\Property(example: '2025-01-15')]
    private string $date;

    #[OA\Property(enum: RealizationTimeSlotEnum::VALUES, example: 'morning')]
    private string $timeSlot;

    public function __construct(string $date, string $timeSlot)
    {
        $this->date = $date;
        $this->timeSlot = $timeSlot;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getTimeSlot(): string
    {
        return $this->timeSlot;
    }
}
