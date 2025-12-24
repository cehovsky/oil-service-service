<?php

declare(strict_types=1);

namespace App\Domain;

class Weight
{
    public function __construct(
        private readonly int $weightG,
    ) {
    }

    public static function createFromKgs(string $weightKg): self
    {
        return new self((int) bcmul($weightKg, '1000'));
    }

    public static function createFromGs(int $weightG): self
    {
        return new self($weightG);
    }

    public function getWeightG(): int
    {
        return $this->weightG;
    }

    public function getWeightKg(): string
    {
        return bcdiv((string) $this->weightG, '1000', 3);
    }
}
