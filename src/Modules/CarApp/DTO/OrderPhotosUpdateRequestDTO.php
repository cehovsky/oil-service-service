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

    public function getVinPhotoId(): ?string
    {
        return $this->vinPhotoId;
    }

    public function getOldOilFilterPhotoId(): ?string
    {
        return $this->oldOilFilterPhotoId;
    }

    public function getOldOilPhotoId(): ?string
    {
        return $this->oldOilPhotoId;
    }

    public function getOdometerPhotoId(): ?string
    {
        return $this->odometerPhotoId;
    }

    /**
     * @return string[]
     */
    public function getOtherPhotoIds(): array
    {
        return $this->otherPhotoIds;
    }
}