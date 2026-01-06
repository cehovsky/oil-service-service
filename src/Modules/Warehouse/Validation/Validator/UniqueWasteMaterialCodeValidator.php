<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\Validation\Validator;

use App\Modules\Warehouse\Validation\Constraint\UniqueWasteMaterialCode;
use App\Warehouse\DBAL\Entity\WasteMaterial;
use App\Warehouse\DBAL\Repository\WasteMaterialRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueWasteMaterialCodeValidator extends ConstraintValidator
{
    public function __construct(
        private readonly WasteMaterialRepository $wasteMaterialRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueWasteMaterialCode) {
            throw new UnexpectedTypeException($constraint, UniqueWasteMaterialCode::class);
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

        $wasteMaterial = $this->wasteMaterialRepository->findByCode($code);

        if ($wasteMaterial === null) {
            return;
        }

        if ($constraint->ignoreWasteMaterialId !== null && $this->isSameWasteMaterial($wasteMaterial, $constraint->ignoreWasteMaterialId)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('code')
            ->addViolation();
    }

    private function isSameWasteMaterial(WasteMaterial $wasteMaterial, string $ignoreWasteMaterialId): bool
    {
        return $wasteMaterial->getId()->__toString() === $ignoreWasteMaterialId;
    }
}
