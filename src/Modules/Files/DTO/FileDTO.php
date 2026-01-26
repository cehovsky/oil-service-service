<?php

declare(strict_types=1);

namespace App\Modules\Files\DTO;

use OpenApi\Attributes as OA;

class FileDTO
{
    #[OA\Property(example: 'b3063b38-1f4f-48c7-b885-88702dbb7349')]
    private string $id;

    #[OA\Property(description: 'Original name of the file', example: 'invoice.pdf')]
    private string $fileName;

    #[OA\Property(example: 102400)]
    private int $size;

    #[OA\Property(example: '2026-01-26T10:00:00+00:00')]
    private string $createdAt;

    public function __construct(
        string $id,
        string $fileName,
        int $size,
        string $createdAt,
    ) {
        $this->id = $id;
        $this->fileName = $fileName;
        $this->size = $size;
        $this->createdAt = $createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
