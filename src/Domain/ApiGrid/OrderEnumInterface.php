<?php

namespace App\Domain\ApiGrid;

interface OrderEnumInterface
{
    public function toSql(): string;
}
