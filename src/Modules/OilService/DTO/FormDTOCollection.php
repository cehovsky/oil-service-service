<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\ArrayCollection;

class FormDTOCollection extends ArrayCollection
{
    public function getItemClass(): string
    {
        return FormDTO::class;
    }
}
