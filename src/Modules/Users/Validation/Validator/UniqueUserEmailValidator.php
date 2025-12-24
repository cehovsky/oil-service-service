<?php

declare(strict_types=1);

namespace App\Modules\Users\Validation\Validator;

use App\Auth\DBAL\Entity\User;
use App\Auth\DBAL\Repository\UserRepository;
use App\Modules\Users\Validation\Constraint\UniqueUserEmail;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueUserEmailValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueUserEmail) {
            throw new UnexpectedTypeException($constraint, UniqueUserEmail::class);
        }

        if ($value === null) {
            return;
        }

        if (!is_object($value) || !method_exists($value, 'getEmail')) {
            throw new UnexpectedTypeException($value, 'object with getEmail method');
        }

        $email = $value->getEmail();

        if (!is_string($email) || $email === '') {
            return;
        }

        $user = $this->userRepository->findByEmail($email);

        if ($user === null) {
            return;
        }

        if ($constraint->ignoreUserId !== null && $this->isSameUser($user, $constraint->ignoreUserId)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('email')
            ->addViolation();
    }

    private function isSameUser(User $user, string $ignoreUserId): bool
    {
        return $user->getId()->__toString() === $ignoreUserId;
    }
}
