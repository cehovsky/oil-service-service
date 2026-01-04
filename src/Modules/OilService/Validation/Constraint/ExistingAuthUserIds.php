<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\ExistingAuthUserIdsValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ExistingAuthUserIds extends Constraint
{
    public string $message = 'One or more selected users were not found.';

    public function validatedBy(): string
    {
        return ExistingAuthUserIdsValidator::class;
    }
}
