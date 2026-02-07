<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\ExistingCustomerCarValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ExistingCustomerCar extends Constraint
{
    public string $message = 'Customer car does not exist.';

    public function validatedBy(): string
    {
        return ExistingCustomerCarValidator::class;
    }
}
