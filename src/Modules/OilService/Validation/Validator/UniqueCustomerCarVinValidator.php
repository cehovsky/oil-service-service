<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\UniqueCustomerCarVin;
use App\OilService\DBAL\Repository\CustomerCarRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueCustomerCarVinValidator extends ConstraintValidator
{
    public function __construct(
        private readonly CustomerCarRepository $customerCarRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueCustomerCarVin) {
            throw new UnexpectedTypeException($constraint, UniqueCustomerCarVin::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_object($value) || !method_exists($value, 'getVin')) {
            throw new UnexpectedTypeException($value, 'object with getVin method');
        }

        $vin = $value->getVin();

        if (!is_string($vin) || $vin === '') {
            return;
        }

        $existingCar = $this->customerCarRepository->findOneByVin($vin);

        if ($existingCar === null) {
            return;
        }

        $ignoreCarId = null;
        if (method_exists($value, 'getCustomerCarId')) {
            $ignoreCarId = $value->getCustomerCarId();
        }

        if (is_string($ignoreCarId) && $existingCar->getId()->__toString() === $ignoreCarId) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('vin')
            ->addViolation();
    }
}
