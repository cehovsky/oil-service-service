<?php

declare(strict_types=1);

namespace App\Modules\Users\DTO;

use App\Domain\ArrayCollection;

class UserDTOCollection extends ArrayCollection
{
    public function getItemClass(): string
    {
        return UserDTO::class;
    }
}
