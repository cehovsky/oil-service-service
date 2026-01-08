<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Modules\Users\DTO\UserDTO;
use App\Modules\Warehouse\DTO\StorageContainerMaterialDTO;
use App\Modules\Warehouse\DTO\StorageContainerSummaryDTO;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class RouteDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(ref: new Model(type: CarDTO::class), nullable: true)]
    private ?CarDTO $car;

    #[OA\Property(example: true)]
    private bool $isActive;

    #[OA\Property(example: '2025-01-15')]
    private string $date;

    #[OA\Property(example: '2025-12-30T10:00:00+00:00')]
    private string $createdAt;

    /**
     * @var RouteTermDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: RouteTermDTO::class)))]
    private array $terms;

    /**
     * @var StorageContainerSummaryDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: StorageContainerSummaryDTO::class)))]
    private array $storageContainers;

    /**
     * @var StorageContainerMaterialDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: StorageContainerMaterialDTO::class)))]
    private array $storageContainerMaterials;

    /**
     * @var UserDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: UserDTO::class)))]
    private array $users;

    /**
     * @param RouteTermDTO[] $terms
     * @param StorageContainerSummaryDTO[] $storageContainers
     * @param StorageContainerMaterialDTO[] $storageContainerMaterials
     * @param UserDTO[] $users
     */
    public function __construct(
        string $id,
        ?CarDTO $car,
        bool $isActive,
        string $date,
        string $createdAt,
        array $terms,
        array $storageContainers,
        array $storageContainerMaterials,
        array $users,
    ) {
        $this->id = $id;
        $this->car = $car;
        $this->isActive = $isActive;
        $this->date = $date;
        $this->createdAt = $createdAt;
        $this->terms = $terms;
        $this->storageContainers = $storageContainers;
        $this->storageContainerMaterials = $storageContainerMaterials;
        $this->users = $users;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCar(): ?CarDTO
    {
        return $this->car;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @return RouteTermDTO[]
     */
    public function getTerms(): array
    {
        return $this->terms;
    }

    /**
     * @return StorageContainerSummaryDTO[]
     */
    public function getStorageContainers(): array
    {
        return $this->storageContainers;
    }

    /**
     * @return StorageContainerMaterialDTO[]
     */
    public function getStorageContainerMaterials(): array
    {
        return $this->storageContainerMaterials;
    }

    /**
     * @return UserDTO[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }
}
