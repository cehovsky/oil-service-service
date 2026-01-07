<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Validation\Constraint;

use App\Modules\Warehouse\Validation\Validator\StorageContainerMaterialOriginChoiceValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class StorageContainerMaterialOriginChoice extends Constraint
{
    public string $message = 'Only one of warehouseId or routeId can be provided.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return StorageContainerMaterialOriginChoiceValidator::class;
    }
}
