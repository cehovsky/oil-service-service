<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\AvailableTermSlotValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AvailableTermSlot extends Constraint
{
    public string $termNotFoundMessage = 'The selected date and time slot is not available.';

    public string $termCapacityExceededMessage = 'The selected date and time slot is fully booked.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return AvailableTermSlotValidator::class;
    }
}
