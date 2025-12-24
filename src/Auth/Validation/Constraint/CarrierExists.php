<?php

declare(strict_types=1);

namespace App\Auth\Validation\Constraint;

use App\Auth\Validation\Validator\CarrierExistsValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class CarrierExists extends Constraint
{
    public string $messageDoesNotExist = 'This carrier does not exist.';

    public string $messageManaged = 'This carrier is managed by: {{ managed }}';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return CarrierExistsValidator::class;
    }
}
