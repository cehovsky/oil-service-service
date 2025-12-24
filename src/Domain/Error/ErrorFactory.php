<?php

declare(strict_types=1);

namespace App\Domain\Error;

use App\Domain\Type\TypeResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ErrorFactory
{
    public function createErrorCollectionFromConstraintViolationList(
        ConstraintViolationListInterface $constraintViolationList,
    ): ErrorCollection {
        $errorCollection = new ErrorCollection();

        if ($constraintViolationList->count() <= 0) {
            return $errorCollection;
        }

        foreach ($constraintViolationList as $violation) {
            $code = (string) $violation->getCode();
            // For some reason, symfony validators return UUID as violation code. This fixes that.
            if (TypeResolver::isUuid($code)) {
                $code = 'invalid.' . ucfirst($violation->getPropertyPath());
            }

            $errorItem = $this->createErrorItem(
                (string) $violation->getMessage(),
                $code,
                $violation->getPropertyPath(),
            );
            $errorCollection->add($errorItem);
        }

        return $errorCollection;
    }

    public function createErrorCollectionWithSimpleError(
        string $message,
        string $code,
        ?string $path = null,
    ): ErrorCollection {
        $errorCollection = new ErrorCollection();

        $errorItem = $this->createErrorItem(
            $message,
            $code,
            $path,
        );

        $errorCollection->add($errorItem);

        return $errorCollection;
    }

    public function createErrorItem(string $message, string $code, ?string $path): ErrorItem
    {
        if ($code === Choice::NO_SUCH_CHOICE_ERROR) {
            $code = 'noSuchChoice';
        }

        return new ErrorItem($message, $code, $path);
    }
}
