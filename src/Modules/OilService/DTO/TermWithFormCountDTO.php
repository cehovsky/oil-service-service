<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use OpenApi\Attributes as OA;

class TermWithFormCountDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: '2025-01-15')]
    private string $date;

    #[OA\Property(enum: RealizationTimeSlotEnum::VALUES, example: 'morning')]
    private string $timeSlot;

    #[OA\Property(example: true)]
    private bool $isActive;

    #[OA\Property(example: 10)]
    private int $maxCount;

    #[OA\Property(example: 3)]
    private int $formCount;

    #[OA\Property(example: '2025-12-30T10:00:00+00:00')]
    private string $createdAt;

    public function __construct(
        string $id,
        string $date,
        string $timeSlot,
        bool $isActive,
        int $maxCount,
        int $formCount,
        string $createdAt,
    ) {
        $this->id = $id;
        $this->date = $date;
        $this->timeSlot = $timeSlot;
        $this->isActive = $isActive;
        $this->maxCount = $maxCount;
        $this->formCount = $formCount;
        $this->createdAt = $createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getTimeSlot(): string
    {
        return $this->timeSlot;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getMaxCount(): int
    {
        return $this->maxCount;
    }

    public function getFormCount(): int
    {
        return $this->formCount;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
