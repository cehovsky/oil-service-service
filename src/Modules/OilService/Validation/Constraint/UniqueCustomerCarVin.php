<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\UniqueCustomerCarVinValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueCustomerCarVin extends Constraint
{
    public string $message = 'VIN cannot be used, duplicate item.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return UniqueCustomerCarVinValidator::class;
    }
}
