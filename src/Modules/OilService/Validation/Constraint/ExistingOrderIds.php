<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\ExistingOrderIdsValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ExistingOrderIds extends Constraint
{
    public string $message = 'One or more selected orders were not found.';

    public function validatedBy(): string
    {
        return ExistingOrderIdsValidator::class;
    }
}
