<?php

declare(strict_types=1);

namespace App\Modules\OilService\Factory;

use App\Domain\DTOValueResolver;
use App\Modules\OilService\DTO\FormCreateResponseDTO;

class DTOFactory
{
    public function createFormCreateResponseDTO(): FormCreateResponseDTO
    {
        return new FormCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            true,
        );
    }
}
