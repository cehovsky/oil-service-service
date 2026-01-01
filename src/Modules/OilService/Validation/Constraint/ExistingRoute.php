<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\ExistingRouteValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ExistingRoute extends Constraint
{
    public string $message = 'Selected route was not found.';

    public function validatedBy(): string
    {
        return ExistingRouteValidator::class;
    }
}
