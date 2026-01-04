<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class StorageContainerLocationCreateResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735980000)]
    private int $timestamp;

    #[OA\Property(ref: new Model(type: StorageContainerLocationDTO::class))]
    private StorageContainerLocationDTO $storageContainerLocation;

    public function __construct(string $result, int $timestamp, StorageContainerLocationDTO $storageContainerLocation)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->storageContainerLocation = $storageContainerLocation;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getStorageContainerLocation(): StorageContainerLocationDTO
    {
        return $this->storageContainerLocation;
    }
}
