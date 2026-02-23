<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\ResolvableServiceAddress;
use App\OilService\ServiceArea\ServiceAreaAddressEvaluationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ResolvableServiceAddressValidator extends ConstraintValidator
{
    public function __construct(private readonly ServiceAreaAddressEvaluationService $addressEvaluationService)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ResolvableServiceAddress) {
            throw new UnexpectedTypeException($constraint, ResolvableServiceAddress::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $evaluation = $this->addressEvaluationService->evaluateAddress($value);

        if ($evaluation->isRecognized()) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->addViolation();
    }
}
