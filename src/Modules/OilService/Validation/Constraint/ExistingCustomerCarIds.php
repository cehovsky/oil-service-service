<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\ExistingCustomerCarIdsValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ExistingCustomerCarIds extends Constraint
{
    public string $message = 'Some customer car ids do not exist.';

    public function validatedBy(): string
    {
        return ExistingCustomerCarIdsValidator::class;
    }
}
