<?php

namespace App\Core\Factory;

use App\Core\DTO\ErrorResponseDTO;

class DTOFactory
{
    public function createErrorResponseDTO(
        string $result
    ): ErrorResponseDTO {
        return new ErrorResponseDTO($result);
    }
}
