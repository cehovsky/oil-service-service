<?php

declare(strict_types=1);

namespace App\Domain\Validation\Constraint;

use App\Domain\Validation\Validator\Iso8601DateTimeValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER | Attribute::TARGET_METHOD)]
class Iso8601DateTime extends Constraint
{
    public string $message = 'This value is not a valid datetime.';

    /**
     * @param array<string>|null $groups
     * @param array<string, mixed>|null $options
     */
    public function __construct(
        public bool $allowDateOnly = false,
        ?string $message = null,
        ?array $groups = null,
        mixed $payload = null,
        ?array $options = null,
    ) {
        parent::__construct($options ?? [], $groups, $payload);

        if ($message !== null) {
            $this->message = $message;
        }
    }

    public function validatedBy(): string
    {
        return Iso8601DateTimeValidator::class;
    }
}
