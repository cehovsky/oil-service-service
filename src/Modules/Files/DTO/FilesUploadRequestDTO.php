<?php

declare(strict_types=1);

namespace App\Modules\Files\DTO;

use App\Domain\Validation\Base64Constraint;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

class FilesUploadRequestDTO
{
    #[OA\Property(
        description: 'File name, including file extension',
        pattern: '^.+\.\w+$',
        example: 'test.docx',
    )]
    #[Assert\NotBlank]
    #[Assert\Regex('~^.+\.\w+$~')]
    private string $fileName;

    #[OA\Property(
        description: 'File contents in Base64',
        example: 'YmVsc2vDqSDDs2R5Lg==',
    )]
    #[Base64Constraint]
    #[Assert\NotBlank]
    private string $contents;

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    #[Ignore]
    public function getContentsAsB64(): string
    {
        return $this->contents;
    }

    #[Ignore]
    public function getContentsAsBinary(): string
    {
        return base64_decode($this->contents);
    }

    public function setContents(string $contents): void
    {
        $this->contents = $contents;
    }
}
