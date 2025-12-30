<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\UniqueOilServiceUserEmailValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueOilServiceUserEmail extends Constraint
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
        return UniqueOilServiceUserEmailValidator::class;
    }
}
