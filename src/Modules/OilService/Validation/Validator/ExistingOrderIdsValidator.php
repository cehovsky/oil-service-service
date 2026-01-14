<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\ExistingOrderIds;
use App\OilService\DBAL\Repository\OrderRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistingOrderIdsValidator extends ConstraintValidator
{
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingOrderIds) {
            throw new UnexpectedTypeException($constraint, ExistingOrderIds::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        foreach ($value as $orderId) {
            if (!is_string($orderId) || $orderId === '') {
                throw new UnexpectedTypeException($orderId, 'string');
            }

            if ($this->orderRepository->find($orderId) === null) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();

                return;
            }
        }
    }
}
