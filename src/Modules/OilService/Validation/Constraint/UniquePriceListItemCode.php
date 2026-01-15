<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Constraint;

use App\Modules\OilService\Validation\Validator\UniquePriceListItemCodeValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniquePriceListItemCode extends Constraint
{
    public string $message = 'Code cannot be used, duplicate item.';

    public function __construct(
        public readonly ?string $ignorePriceListItemId = null,
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
        return UniquePriceListItemCodeValidator::class;
    }
}
