<?php

declare(strict_types=1);

namespace App\Domain\ApiGrid;

enum OrderEnum: string
{
    case ASC = 'ASC';

    case DESC = 'DESC';
}
