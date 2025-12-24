<?php

declare(strict_types=1);

namespace App\Modules\Files\DTO;

use App\Domain\DTOValueResolver;
use OpenApi\Attributes as OA;

class FileUploadResponseDTO
{
    public function __construct(
        #[OA\Property(
            example: DTOValueResolver::RESULT_SUCCESS,
        )]
        private readonly string $result,
        #[OA\Property(
            description: 'Original name of the file',
            example: 'test.xlsx',
        )]
        private readonly string $fileName,
        #[OA\Property(
            description: 'Generated file handle',
            example: 'b3063b38-1f4f-48c7-b885-88702dbb7349',
        )]
        private readonly ?string $handle,
    ) {
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getHandle(): ?string
    {
        return $this->handle;
    }
}
