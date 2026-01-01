<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\UniqueTermDateTimeSlotValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueTermDateTimeSlot extends Constraint
{
    public string $message = 'A term for the selected date and time slot already exists.';

    public function __construct(
        public readonly ?string $ignoreTermId = null,
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
        return UniqueTermDateTimeSlotValidator::class;
    }
}
