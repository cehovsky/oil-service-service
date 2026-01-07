<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Validation\Validator;

use App\Modules\Warehouse\Validation\Constraint\PreferredWasteMaterialForStorageContainer;
use App\Warehouse\DBAL\Repository\StorageContainerRepository;
use App\Warehouse\DBAL\Repository\WasteMaterialRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PreferredWasteMaterialForStorageContainerValidator extends ConstraintValidator
{
    public function __construct(
        private readonly StorageContainerRepository $storageContainerRepository,
        private readonly WasteMaterialRepository $wasteMaterialRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PreferredWasteMaterialForStorageContainer) {
            throw new UnexpectedTypeException($constraint, PreferredWasteMaterialForStorageContainer::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_object($value) || !method_exists($value, 'getStorageContainerId') || !method_exists($value, 'getWasteMaterialId')) {
            throw new UnexpectedTypeException($value, 'object with getStorageContainerId and getWasteMaterialId methods');
        }

        $storageContainerId = $value->getStorageContainerId();
        $wasteMaterialId = $value->getWasteMaterialId();

        if (!is_string($storageContainerId) || $storageContainerId === '') {
            return;
        }

        if (!is_string($wasteMaterialId) || $wasteMaterialId === '') {
            return;
        }

        $storageContainer = $this->storageContainerRepository->find($storageContainerId);
        $wasteMaterial = $this->wasteMaterialRepository->find($wasteMaterialId);

        if ($storageContainer === null || $wasteMaterial === null) {
            return;
        }

        if ($storageContainer->getPreferredWasteMaterials()->contains($wasteMaterial)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('wasteMaterialId')
            ->addViolation();
    }
}
