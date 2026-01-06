<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Validation\Validator;

use App\Modules\Warehouse\Validation\Constraint\UniqueStorageContainerCode;
use App\Warehouse\DBAL\Entity\StorageContainer;
use App\Warehouse\DBAL\Repository\StorageContainerRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueStorageContainerCodeValidator extends ConstraintValidator
{
    public function __construct(
        private readonly StorageContainerRepository $storageContainerRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueStorageContainerCode) {
            throw new UnexpectedTypeException($constraint, UniqueStorageContainerCode::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_object($value) || !method_exists($value, 'getCode')) {
            throw new UnexpectedTypeException($value, 'object with getCode method');
        }

        $code = $value->getCode();

        if (!is_string($code) || $code === '') {
            return;
        }

        $storageContainer = $this->storageContainerRepository->findByCode($code);

        if ($storageContainer === null) {
            return;
        }

        if ($constraint->ignoreStorageContainerId !== null && $this->isSameStorageContainer($storageContainer, $constraint->ignoreStorageContainerId)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('code')
            ->addViolation();
    }

    private function isSameStorageContainer(StorageContainer $storageContainer, string $ignoreStorageContainerId): bool
    {
        return $storageContainer->getId()->__toString() === $ignoreStorageContainerId;
    }
}
