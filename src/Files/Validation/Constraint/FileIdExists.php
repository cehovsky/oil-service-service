<?php

declare(strict_types=1);

namespace App\Files\Validation\Constraint;

use App\Files\Validation\Validator\FileIdExistsValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class FileIdExists extends Constraint
{
    public string $message = 'File id has no exists.';

    public function validatedBy(): string
    {
        return FileIdExistsValidator::class;
    }

    public function getTargets(): string|array
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
