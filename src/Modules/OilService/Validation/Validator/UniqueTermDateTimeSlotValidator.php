<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\UniqueTermDateTimeSlot;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Repository\TermRepository;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueTermDateTimeSlotValidator extends ConstraintValidator
{
    private const string ISO8601_WITH_OPTIONAL_TIME_PATTERN = '/^\d{4}-\d{2}-\d{2}(?:T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?)?$/';

    public function __construct(
        private readonly TermRepository $termRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueTermDateTimeSlot) {
            throw new UnexpectedTypeException($constraint, UniqueTermDateTimeSlot::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_object($value) || !method_exists($value, 'getDate') || !method_exists($value, 'getTimeSlot')) {
            throw new UnexpectedTypeException($value, 'object with getDate and getTimeSlot methods');
        }

        $date = $this->createDate($value->getDate());
        $timeSlot = $this->createTimeSlot($value->getTimeSlot());

        if ($date === null || $timeSlot === null) {
            return;
        }

        $exists = $this->termRepository->existsByDateAndTimeSlot(
            $date,
            $timeSlot,
            $constraint->ignoreTermId,
        );

        if ($exists) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('timeSlot')
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
