<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class TermCreateRequestDTO
{
    #[OA\Property(example: '2025-01-15', description: 'Date in format YYYY-MM-DD')]
    #[Assert\NotBlank]
    #[Assert\Date]
    private string $date;

    #[OA\Property(enum: RealizationTimeSlotEnum::VALUES, example: 'morning')]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [RealizationTimeSlotEnum::class, 'values'])]
    private string $timeSlot;

    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isActive;

    #[OA\Property(example: 10)]
    #[Assert\NotNull]
    #[Assert\Positive]
    private int $maxCount;

    public function getDate(): string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTimeSlot(): string
    {
        return $this->timeSlot;
    }

    public function setTimeSlot(string $timeSlot): self
    {
        $this->timeSlot = $timeSlot;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getMaxCount(): int
    {
        return $this->maxCount;
    }

    public function setMaxCount(int $maxCount): self
    {
        $this->maxCount = $maxCount;

        return $this;
    }
}
