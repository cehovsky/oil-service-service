<?php

declare(strict_types=1);

namespace App\Modules\OilService\Validation\Validator;

use App\Modules\OilService\Validation\Constraint\ExistingRoute;
use App\OilService\DBAL\Repository\RouteRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExistingRouteValidator extends ConstraintValidator
{
    public function __construct(
        private readonly RouteRepository $routeRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingRoute) {
            throw new UnexpectedTypeException($constraint, ExistingRoute::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_string($value) || $value === '') {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ($this->routeRepository->find($value) === null) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
