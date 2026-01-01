<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\ExistingCarValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ExistingCar extends Constraint
{
    public string $message = 'Selected car was not found.';

    public function validatedBy(): string
    {
        return ExistingCarValidator::class;
    }
}
