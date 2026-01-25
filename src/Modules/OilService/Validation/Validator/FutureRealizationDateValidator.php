<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\FutureRealizationDate;
use App\OilService\Term\TermAvailabilityPolicy;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class FutureRealizationDateValidator extends ConstraintValidator
{
    private const string ISO8601_WITH_OPTIONAL_TIME_PATTERN = '/^\d{4}-\d{2}-\d{2}(?:T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?)?$/';

    public function __construct(
        private readonly TermAvailabilityPolicy $termAvailabilityPolicy,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof FutureRealizationDate) {
            throw new UnexpectedTypeException($constraint, FutureRealizationDate::class);
        }

        $date = $this->createDate($value);

        if ($date === null) {
            return;
        }

        $minimumAvailableDate = $this->termAvailabilityPolicy->getMinimumAvailableDate();

        if ($date < $minimumAvailableDate) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    private function createDate(mixed $value): ?DateTimeImmutable
    {
        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ($value === '' || !preg_match(self::ISO8601_WITH_OPTIONAL_TIME_PATTERN, $value)) {
            return null;
        }

        try {
            $dateTime = new DateTimeImmutable($value);
        } catch (Exception) {
            return null;
        }

        return DateTimeImmutable::createFromFormat('!Y-m-d', $dateTime->format('Y-m-d')) ?: null;
    }
}
