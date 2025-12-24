<?php

declare(strict_types=1);

namespace App\Domain\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class Base64Constraint extends Constraint
{
    public function __construct()
    {
        parent::__construct();
    }

    public function validatedBy(): string
    {
        return Base64Validator::class;
    }

    public function getMessage(): string
    {
        return 'The value "{{ string }}" is not a valid Base64 string.';
    }
}
