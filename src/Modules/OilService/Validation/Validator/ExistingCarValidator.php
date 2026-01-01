<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\ExistingCar;
use App\OilService\DBAL\Repository\CarRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistingCarValidator extends ConstraintValidator
{
    public function __construct(
        private readonly CarRepository $carRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingCar) {
            throw new UnexpectedTypeException($constraint, ExistingCar::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_string($value) || $value === '') {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ($this->carRepository->find($value) === null) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
