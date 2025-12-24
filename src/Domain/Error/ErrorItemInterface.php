<?php

namespace App\Domain\Error;

interface ErrorItemInterface
{
    public function getMessage(): string;

    public function getPath(): ?string;
}
