<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class StorageContainerUpdateResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735980000)]
    private int $timestamp;

    #[OA\Property(ref: new Model(type: StorageContainerDTO::class))]
    private StorageContainerDTO $storageContainer;

    public function __construct(string $result, int $timestamp, StorageContainerDTO $storageContainer)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->storageContainer = $storageContainer;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getStorageContainer(): StorageContainerDTO
    {
        return $this->storageContainer;
    }
}
