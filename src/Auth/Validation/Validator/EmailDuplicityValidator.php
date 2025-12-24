<?php

declare(strict_types=1);

namespace App\Auth\Validation\Validator;

use App\Auth\DBAL\Entity\User;
use App\Auth\DBAL\Repository\UserRepository;
use App\Auth\Validation\Constraint\EmailDuplicity;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\Proxy;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EmailDuplicityValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * @param User|null $value
     *
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof EmailDuplicity) {
            throw new UnexpectedTypeException($constraint, EmailDuplicity::class);
        }

        if ($value === null) {
            return;
        }

        if (!$value instanceof User) {
            throw new UnexpectedTypeException($value, User::class);
        }

        $email = $value->getEmail();
        $userId = null;

        if ($value instanceof Proxy && !$value->__isInitialized()) {
            try {
                $value->__load();
                assert($value instanceof User);
                $userId = $value->getId();
            } catch (EntityNotFoundException) {
                // pass
            }
        }

        if ($userId === null) {
            $user = $this->userRepository->findByEmail($email);

            if ($user !== null) {
                $this->context->buildViolation($constraint->message)
                    ->atPath($constraint->message)
                    ->addViolation();
            }

            return;
        }

        $users = $this->userRepository->findUsersByEmailWithNeqId($email, $userId);

        if ($users !== []) {
            $this->context->buildViolation($constraint->message)
                ->atPath($constraint->message)
                ->addViolation();
        }
    }
}
