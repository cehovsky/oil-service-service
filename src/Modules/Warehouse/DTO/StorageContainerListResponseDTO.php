<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class StorageContainerListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var StorageContainerDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: StorageContainerDTO::class)))]
    private array $storageContainers;

    private int $pageCount;

    /**
     * @param StorageContainerDTO[] $storageContainers
     */
    public function __construct(string $result, int $timestamp, array $storageContainers, int $pageCount)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->storageContainers = $storageContainers;
        $this->pageCount = $pageCount;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return StorageContainerDTO[]
     */
    public function getStorageContainers(): array
    {
        return $this->storageContainers;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
