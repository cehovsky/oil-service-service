<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\AvailableTermSlot;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Repository\FormRepository;
use App\OilService\DBAL\Repository\TermRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AvailableTermSlotValidator extends ConstraintValidator
{
    private const string ISO8601_WITH_OPTIONAL_TIME_PATTERN = '/^\d{4}-\d{2}-\d{2}(?:T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?)?$/';

    public function __construct(
        private readonly TermRepository $termRepository,
        private readonly FormRepository $formRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof AvailableTermSlot) {
            throw new UnexpectedTypeException($constraint, AvailableTermSlot::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_object($value) || !method_exists($value, 'getRealizationDate') || !method_exists($value, 'getRealizationTimeSlot')) {
            throw new UnexpectedTypeException($value, 'object with getRealizationDate and getRealizationTimeSlot methods');
        }

        $date = $this->createDate($value->getRealizationDate());
        $timeSlot = $this->createTimeSlot($value->getRealizationTimeSlot());

        if ($date === null || $timeSlot === null) {
            return;
        }

        $term = $this->termRepository->findOneBy([
            'date' => $date,
            'timeSlot' => $timeSlot,
            'isActive' => true,
        ]);

        if ($term === null) {
            $this->context
                ->buildViolation($constraint->termNotFoundMessage)
                ->atPath('realizationTimeSlot')
                ->addViolation();

            return;
        }

        $currentCount = $this->formRepository->countActiveByDateAndTimeSlot($date, $timeSlot);

        if ($currentCount >= $term->getMaxCount()) {
            $this->context
                ->buildViolation($constraint->termCapacityExceededMessage)
                ->atPath('realizationTimeSlot')
                ->addViolation();
        }
    }

    private function createDate(mixed $dateString): ?DateTimeImmutable
    {
        if (!is_string($dateString) || $dateString === '') {
            return null;
        }

        if (!preg_match(self::ISO8601_WITH_OPTIONAL_TIME_PATTERN, $dateString)) {
            return null;
        }

        try {
            $dateTime = new DateTimeImmutable($dateString);
        } catch (Exception) {
            return null;
        }

        return DateTimeImmutable::createFromFormat('!Y-m-d', $dateTime->format('Y-m-d')) ?: null;
    }

    private function createTimeSlot(mixed $timeSlot): ?RealizationTimeSlotEnum
    {
        if (!is_string($timeSlot) || $timeSlot === '') {
            return null;
        }

        return RealizationTimeSlotEnum::tryFrom($timeSlot);
    }
}
