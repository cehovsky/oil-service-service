<?php

declare(strict_types=1);

namespace App\Auth\Validation\Constraint;

use App\Auth\Validation\Validator\EshopExistsValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class EshopExists extends Constraint
{
    public string $message = 'Eshop ID does not exist.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return EshopExistsValidator::class;
    }
}
