<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\ExistingTermIds;
use App\OilService\DBAL\Repository\TermRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistingTermIdsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TermRepository $termRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingTermIds) {
            throw new UnexpectedTypeException($constraint, ExistingTermIds::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        foreach ($value as $termId) {
            if (!is_string($termId) || $termId === '') {
                throw new UnexpectedTypeException($termId, 'string');
            }

            if ($this->termRepository->find($termId) === null) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();

                return;
            }
        }
    }
}
