<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\ExistingStorageContainerIds;
use App\Warehouse\DBAL\Repository\StorageContainerRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistingStorageContainerIdsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly StorageContainerRepository $storageContainerRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingStorageContainerIds) {
            throw new UnexpectedTypeException($constraint, ExistingStorageContainerIds::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        foreach ($value as $storageContainerId) {
            if (!is_string($storageContainerId) || $storageContainerId === '') {
                throw new UnexpectedTypeException($storageContainerId, 'string');
            }

            if ($this->storageContainerRepository->find($storageContainerId) === null) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();

                return;
            }
        }
    }
}
