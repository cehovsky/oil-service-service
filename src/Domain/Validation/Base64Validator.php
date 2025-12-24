<?php

declare(strict_types=1);

namespace App\Domain\Validation;

use Override;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class Base64Validator extends ConstraintValidator
{
    #[Override] public function validate(mixed $value, Constraint $constraint): void
    {
        assert($constraint instanceof Base64Constraint);

        if (!$value) {
            return;
        }
        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!base64_decode($value)) {
            $this->context->buildViolation($constraint->getMessage())
                ->setParameter('{{ string }}', substr($value, 0, 50) . (strlen($value) > 50 ? '...' : ''))
                ->addViolation();
        }
    }
}
