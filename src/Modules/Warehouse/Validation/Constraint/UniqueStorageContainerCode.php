<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Validation\Constraint;

use App\Modules\Warehouse\Validation\Validator\UniqueStorageContainerCodeValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueStorageContainerCode extends Constraint
{
    public string $message = 'Code cannot be used, duplicate item.';

    public function __construct(
        public readonly ?string $ignoreStorageContainerId = null,
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
        return UniqueStorageContainerCodeValidator::class;
    }
}
