<?php

declare(strict_types=1);

namespace App\Auth\Validation\Validator;

use App\Auth\Validation\Constraint\CarrierExists;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CarrierExistsValidator extends ConstraintValidator
{
    /**
     * @param string|null $value
     * @param CarrierExists $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CarrierExists) {
            throw new UnexpectedTypeException($constraint, CarrierExists::class);
        }

        if ($value === null) {
            return;
        }
    }
}
