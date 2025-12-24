<?php

namespace App\Domain\Error;

interface ErrorCollectionInterface
{
    /**
     * @return ErrorItemInterface[]
     */
    public function toArray(): array;
}
