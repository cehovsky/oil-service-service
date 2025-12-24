<?php

declare(strict_types=1);

namespace App\Auth\Validation\Validator;

use App\Auth\Validation\Constraint\EshopExists;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EshopExistsValidator extends ConstraintValidator
{
    /**
     * @throws UnexpectedTypeException
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof EshopExists) {
            throw new UnexpectedTypeException($constraint, EshopExists::class);
        }

        if ($value === null) {
            return;
        }
    }
}
