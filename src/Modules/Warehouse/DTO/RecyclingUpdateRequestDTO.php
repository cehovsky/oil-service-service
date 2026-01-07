<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class RecyclingUpdateRequestDTO
{
    #[OA\Property(example: '2026-01-07', nullable: true)]
    #[Assert\Date]
    private ?string $recycledAt = null;

    /**
     * @var string[]|null
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string', example: '2aab3a6d-1bde-47b2-8f12-a6120f8470c0'), nullable: true)]
    #[Assert\All([
        new Assert\Uuid(),
    ])]
    private ?array $storageContainerIds = null;

    public function getRecycledAt(): ?string
    {
        return $this->recycledAt;
    }

    public function setRecycledAt(?string $recycledAt): self
    {
        $this->recycledAt = $recycledAt;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getStorageContainerIds(): ?array
    {
        return $this->storageContainerIds;
    }

    /**
     * @param string[]|null $storageContainerIds
     */
    public function setStorageContainerIds(?array $storageContainerIds): self
    {
        $this->storageContainerIds = $storageContainerIds;

        return $this;
    }
}
