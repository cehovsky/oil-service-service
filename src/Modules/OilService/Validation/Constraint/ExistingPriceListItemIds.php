<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\ExistingPriceListItemIdsValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ExistingPriceListItemIds extends Constraint
{
    public string $message = 'One or more selected price list items were not found.';

    public function validatedBy(): string
    {
        return ExistingPriceListItemIdsValidator::class;
    }
}
