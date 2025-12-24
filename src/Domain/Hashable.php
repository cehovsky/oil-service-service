<?php

declare(strict_types=1);

namespace App\Domain;

interface Hashable
{
    public function getHash(): string;
}
