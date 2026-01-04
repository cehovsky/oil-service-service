<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Auth\DBAL\Repository\UserRepository;
use App\Modules\OilService\Validation\Constraint\ExistingAuthUserIds;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistingAuthUserIdsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingAuthUserIds) {
            throw new UnexpectedTypeException($constraint, ExistingAuthUserIds::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        foreach ($value as $userId) {
            if (!is_string($userId) || $userId === '') {
                throw new UnexpectedTypeException($userId, 'string');
            }

            if ($this->userRepository->find($userId) === null) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();

                return;
            }
        }
    }
}
