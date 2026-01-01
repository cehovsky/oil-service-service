<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\ExistingTermIdsValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ExistingTermIds extends Constraint
{
    public string $message = 'One or more selected terms were not found.';

    public function validatedBy(): string
    {
        return ExistingTermIdsValidator::class;
    }
}
