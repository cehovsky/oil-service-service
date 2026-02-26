<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class PublicOrderReportPhotoDTO
{
    #[OA\Property(example: 'Fotografie vozidla')]
    private string $label;

    #[OA\Property(example: 'before.jpg')]
    private string $fileName;

    #[OA\Property(example: '/files/download/0d77e1ad-e7ed-4ddb-81cd-f18d0ca2b4d4')]
    private string $downloadPath;

    public function __construct(string $label, string $fileName, string $downloadPath)
    {
        $this->label = $label;
        $this->fileName = $fileName;
        $this->downloadPath = $downloadPath;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getDownloadPath(): string
    {
        return $this->downloadPath;
    }
}
