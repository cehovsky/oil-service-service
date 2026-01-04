<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class StorageContainerLocationListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var StorageContainerLocationDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: StorageContainerLocationDTO::class)))]
    private array $storageContainerLocations;

    private int $pageCount;

    /**
     * @param StorageContainerLocationDTO[] $storageContainerLocations
     */
    public function __construct(string $result, int $timestamp, array $storageContainerLocations, int $pageCount)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->storageContainerLocations = $storageContainerLocations;
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
     * @return StorageContainerLocationDTO[]
     */
    public function getStorageContainerLocations(): array
    {
        return $this->storageContainerLocations;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
