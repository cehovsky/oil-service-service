<?php

declare(strict_types=1);

namespace App\Domain\Validation\Validator;

use App\Domain\Validation\Constraint\Iso8601DateTime;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class Iso8601DateTimeValidator extends ConstraintValidator
{
    private const string PATTERN_WITH_TIME = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})$/';
    private const string PATTERN_OPTIONAL_TIME = '/^\d{4}-\d{2}-\d{2}(?:T\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:Z|[+-]\d{2}:\d{2})?)?$/';

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Iso8601DateTime) {
            throw new UnexpectedTypeException($constraint, Iso8601DateTime::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $pattern = $constraint->allowDateOnly
            ? self::PATTERN_OPTIONAL_TIME
            : self::PATTERN_WITH_TIME;

        if (!preg_match($pattern, $value)) {
            $this->context->buildViolation($constraint->message)->addViolation();

            return;
        }

        try {
            new DateTimeImmutable($value);
        } catch (Exception) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
