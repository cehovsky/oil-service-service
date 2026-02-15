<?php

declare(strict_types=1);

namespace App\Modules\CarApp\DTO;

use App\Files\Validation\Constraint\FileIdExists;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class OrderPhotosUpdateRequestDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7', nullable: true)]
    #[Assert\Uuid]
    #[FileIdExists]
    private ?string $oilChangeVehiclePhotoId = null;

    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7', nullable: true)]
    #[Assert\Uuid]
    #[FileIdExists]
    private ?string $vinPhotoId = null;

    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7', nullable: true)]
    #[Assert\Uuid]
    #[FileIdExists]
    private ?string $oldOilFilterPhotoId = null;

    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7', nullable: true)]
    #[Assert\Uuid]
    #[FileIdExists]
    private ?string $oldOilPhotoId = null;

    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7', nullable: true)]
    #[Assert\Uuid]
    #[FileIdExists]
    private ?string $odometerPhotoId = null;

    #[OA\Property(example: 123456, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $mileage = null;

    /**
     * @var string[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string', example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7'))]
    #[Assert\NotNull]
    #[Assert\All([
        new Assert\Uuid(),
        new FileIdExists(),
    ])]
    private array $otherPhotoIds = [];

    public function getOilChangeVehiclePhotoId(): ?string
    {
        return $this->oilChangeVehiclePhotoId;
    }

    public function setOilChangeVehiclePhotoId(?string $oilChangeVehiclePhotoId): self
    {
        $this->oilChangeVehiclePhotoId = $oilChangeVehiclePhotoId;

        return $this;
    }

    public function getVinPhotoId(): ?string
    {
        return $this->vinPhotoId;
    }

    public function setVinPhotoId(?string $vinPhotoId): self
    {
        $this->vinPhotoId = $vinPhotoId;

        return $this;
    }

    public function getOldOilFilterPhotoId(): ?string
    {
        return $this->oldOilFilterPhotoId;
    }

    public function setOldOilFilterPhotoId(?string $oldOilFilterPhotoId): self
    {
        $this->oldOilFilterPhotoId = $oldOilFilterPhotoId;

        return $this;
    }

    public function getOldOilPhotoId(): ?string
    {
        return $this->oldOilPhotoId;
    }

    public function setOldOilPhotoId(?string $oldOilPhotoId): self
    {
        $this->oldOilPhotoId = $oldOilPhotoId;

        return $this;
    }

    public function getOdometerPhotoId(): ?string
    {
        return $this->odometerPhotoId;
    }

    public function setOdometerPhotoId(?string $odometerPhotoId): self
    {
        $this->odometerPhotoId = $odometerPhotoId;

        return $this;
    }

    public function getMileage(): ?int
    {
        return $this->mileage;
    }

    public function setMileage(?int $mileage): self
    {
        $this->mileage = $mileage;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getOtherPhotoIds(): array
    {
        return $this->otherPhotoIds;
    }

    /**
     * @param string[] $otherPhotoIds
     */
    public function setOtherPhotoIds(array $otherPhotoIds): self
    {
        $this->otherPhotoIds = $otherPhotoIds;

        return $this;
    }
}
