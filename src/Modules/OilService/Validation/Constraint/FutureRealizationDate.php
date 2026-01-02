<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\FutureRealizationDateValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class FutureRealizationDate extends Constraint
{
    public string $message = 'The realization date must be in the future.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return FutureRealizationDateValidator::class;
    }
}
