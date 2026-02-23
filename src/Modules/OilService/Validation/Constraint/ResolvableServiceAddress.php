<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\ResolvableServiceAddressValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ResolvableServiceAddress extends Constraint
{
    public string $message = 'The service address could not be recognized. Please provide a more precise address.';

    public function validatedBy(): string
    {
        return ResolvableServiceAddressValidator::class;
    }
}
