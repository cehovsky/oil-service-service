<?php

declare(strict_types=1);

namespace App\Modules\Users\Validation\Constraint;

use App\Modules\Users\Validation\Validator\UniqueUserEmailValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueUserEmail extends Constraint
{
    public string $message = 'Email cannot be used, duplicate item.';

    public function __construct(
        public readonly ?string $ignoreUserId = null,
        mixed $options = null,
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return UniqueUserEmailValidator::class;
    }
}
