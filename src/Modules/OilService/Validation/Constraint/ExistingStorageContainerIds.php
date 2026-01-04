<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\ExistingStorageContainerIdsValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ExistingStorageContainerIds extends Constraint
{
    public string $message = 'One or more selected storage containers were not found.';

    public function validatedBy(): string
    {
        return ExistingStorageContainerIdsValidator::class;
    }
}
