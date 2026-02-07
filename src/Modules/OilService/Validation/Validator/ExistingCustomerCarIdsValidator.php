<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\ExistingCustomerCarIds;
use App\OilService\DBAL\Repository\CustomerCarRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistingCustomerCarIdsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly CustomerCarRepository $customerCarRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingCustomerCarIds) {
            throw new UnexpectedTypeException($constraint, ExistingCustomerCarIds::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        $ids = array_values(array_unique(array_filter($value, 'is_string')));

        if ($ids === []) {
            return;
        }

        $cars = $this->customerCarRepository->findBy([
            'id' => $ids,
        ]);

        if (count($cars) === count($ids)) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}
