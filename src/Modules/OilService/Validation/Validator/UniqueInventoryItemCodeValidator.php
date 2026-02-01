<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\UniqueInventoryItemCode;
use App\OilService\DBAL\Entity\InventoryItem;
use App\OilService\DBAL\Repository\InventoryItemRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueInventoryItemCodeValidator extends ConstraintValidator
{
    public function __construct(
        private readonly InventoryItemRepository $inventoryItemRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueInventoryItemCode) {
            throw new UnexpectedTypeException($constraint, UniqueInventoryItemCode::class);
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

        $inventoryItem = $this->inventoryItemRepository->findByCode($code);

        if ($inventoryItem === null) {
            return;
        }

        if ($constraint->ignoreInventoryItemId !== null && $this->isSameInventoryItem($inventoryItem, $constraint->ignoreInventoryItemId)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('code')
            ->addViolation();
    }

    private function isSameInventoryItem(InventoryItem $inventoryItem, string $ignoreInventoryItemId): bool
    {
        return $inventoryItem->getId()->__toString() === $ignoreInventoryItemId;
    }
}
