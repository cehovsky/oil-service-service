<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\ExistingCustomerCar;
use App\OilService\DBAL\Repository\CustomerCarRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistingCustomerCarValidator extends ConstraintValidator
{
    public function __construct(
        private readonly CustomerCarRepository $customerCarRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingCustomerCar) {
            throw new UnexpectedTypeException($constraint, ExistingCustomerCar::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $car = $this->customerCarRepository->find($value);

        if ($car !== null) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
