<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\ExistingPriceListItemIds;
use App\OilService\DBAL\Repository\PriceListItemRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistingPriceListItemIdsValidator extends ConstraintValidator
{
    public function __construct(private readonly PriceListItemRepository $priceListItemRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingPriceListItemIds) {
            throw new UnexpectedTypeException($constraint, ExistingPriceListItemIds::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        foreach ($value as $priceListItemId) {
            if (!is_string($priceListItemId) || $priceListItemId === '') {
                throw new UnexpectedTypeException($priceListItemId, 'string');
            }

            if ($this->priceListItemRepository->find($priceListItemId) === null) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();

                return;
            }
        }
    }
}
