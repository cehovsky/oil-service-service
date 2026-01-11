<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\ExistingOilServiceUser;
use App\OilService\DBAL\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistingOilServiceUserValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingOilServiceUser) {
            throw new UnexpectedTypeException($constraint, ExistingOilServiceUser::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_string($value) || $value === '') {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ($this->userRepository->find($value) === null) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
