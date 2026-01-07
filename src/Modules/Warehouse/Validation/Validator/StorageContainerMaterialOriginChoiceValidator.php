<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Validation\Validator;

use App\Modules\Warehouse\Validation\Constraint\StorageContainerMaterialOriginChoice;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class StorageContainerMaterialOriginChoiceValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof StorageContainerMaterialOriginChoice) {
            throw new UnexpectedTypeException($constraint, StorageContainerMaterialOriginChoice::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_object($value) || !method_exists($value, 'getWarehouseId') || !method_exists($value, 'getRouteId')) {
            throw new UnexpectedTypeException($value, 'object with getWarehouseId and getRouteId methods');
        }

        $warehouseId = $value->getWarehouseId();
        $routeId = $value->getRouteId();

        if ($warehouseId === null || $routeId === null) {
            return;
        }

        if ($warehouseId === '' || $routeId === '') {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('routeId')
            ->addViolation();
    }
}
