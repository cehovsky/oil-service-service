<?php

declare(strict_types=1);

namespace App\Files\Validation\Validator;

use App\Files\DBAL\Repository\FileRepository;
use App\Files\Validation\Constraint\FileIdExists;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Throwable;

class FileIdExistsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly FileRepository $fileRepository
    ) {
    }

    /**
     * @param string|null $value
     * @param FileIdExists $constraint
     *
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof FileIdExists) {
            throw new UnexpectedTypeException($constraint, FileIdExists::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ($this->fileRepository->find($value) === null) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
