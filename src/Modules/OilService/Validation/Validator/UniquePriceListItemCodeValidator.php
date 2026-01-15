<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\UniquePriceListItemCode;
use App\OilService\DBAL\Entity\PriceListItem;
use App\OilService\DBAL\Repository\PriceListItemRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniquePriceListItemCodeValidator extends ConstraintValidator
{
    public function __construct(
        private readonly PriceListItemRepository $priceListItemRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniquePriceListItemCode) {
            throw new UnexpectedTypeException($constraint, UniquePriceListItemCode::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_object($value) || !method_exists($value, 'getCode')) {
            throw new UnexpectedTypeException($value, 'object with getCode method');
        }

        $code = $value->getCode();

        if (!is_string($code) || $code === '') {
            return;
        }

        $priceListItem = $this->priceListItemRepository->findByCode($code);

        if ($priceListItem === null) {
            return;
        }

        if ($constraint->ignorePriceListItemId !== null && $this->isSamePriceListItem($priceListItem, $constraint->ignorePriceListItemId)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('code')
            ->addViolation();
    }

    private function isSamePriceListItem(PriceListItem $priceListItem, string $ignorePriceListItemId): bool
    {
        return $priceListItem->getId()->__toString() === $ignorePriceListItemId;
    }
}
