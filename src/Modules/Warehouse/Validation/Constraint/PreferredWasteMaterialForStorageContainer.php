<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Validation\Constraint;

use App\Modules\Warehouse\Validation\Validator\PreferredWasteMaterialForStorageContainerValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class PreferredWasteMaterialForStorageContainer extends Constraint
{
    public string $message = 'Waste material is not preferred for the selected storage container.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return PreferredWasteMaterialForStorageContainerValidator::class;
    }
}
