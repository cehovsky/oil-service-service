<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class StorageContainerMaterialMoveAllRequestDTO
{
    #[OA\Property(example: 'd1c6d4e5-5c3c-4b4d-9e7c-7f5a4e3d2c1b')]
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private string $targetStorageContainerId;

    public function getTargetStorageContainerId(): string
    {
        return $this->targetStorageContainerId;
    }

    public function setTargetStorageContainerId(string $targetStorageContainerId): self
    {
        $this->targetStorageContainerId = $targetStorageContainerId;

        return $this;
    }
}
