<?php

declare(strict_types=1);

namespace App\Auth\Validation\Constraint;

use App\Auth\Validation\Validator\EmailDuplicityValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class EmailDuplicity extends Constraint
{
    public string $message = 'Email cannot be used, duplicate item.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return EmailDuplicityValidator::class;
    }
}
