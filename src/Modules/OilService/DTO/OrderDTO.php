<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Modules\Files\DTO\FileDTO;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class OrderDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'O2500001')]
    private string $ident;

    #[OA\Property(example: 'Jan Novák')]
    private string $fullName;

    #[OA\Property(example: '+420 123 456 789')]
    private string $phone;

    #[OA\Property(example: 'jan.novak@example.com')]
    private string $email;

    #[OA\Property(example: 'Škoda Octavia 2.0 TDI')]
    private string $carModel;

    #[OA\Property(example: '1A2 3456')]
    private string $licensePlate;

    #[OA\Property(example: 'Václavské náměstí 1, Praha 1, 110 00')]
    private string $address;

    #[OA\Property(example: 'Preferuji odpolední termín', nullable: true)]
    private ?string $note;

    #[OA\Property(example: false)]
    private bool $isCompany;

    #[OA\Property(example: 'Novák s.r.o.', nullable: true)]
    private ?string $companyName;

    #[OA\Property(example: '12345678', nullable: true)]
    private ?string $companyIdentificationNumber;

    #[OA\Property(example: 'CZ12345678', nullable: true)]
    private ?string $companyTaxId;

    #[OA\Property(example: 'Firemní 123, Praha 5, 150 00', nullable: true)]
    private ?string $companyAddress;

    #[OA\Property(ref: new Model(type: FileDTO::class), nullable: true)]
    private ?FileDTO $oilChangeVehiclePhoto;

    #[OA\Property(ref: new Model(type: FileDTO::class), nullable: true)]
    private ?FileDTO $vinPhoto;

    #[OA\Property(ref: new Model(type: FileDTO::class), nullable: true)]
    private ?FileDTO $oldOilFilterPhoto;

    #[OA\Property(ref: new Model(type: FileDTO::class), nullable: true)]
    private ?FileDTO $oldOilPhoto;

    #[OA\Property(ref: new Model(type: FileDTO::class), nullable: true)]
    private ?FileDTO $odometerPhoto;

    /**
     * @var FileDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: FileDTO::class)))]
    private array $otherPhotos;

    #[OA\Property(enum: OrderStatusEnum::VALUES, example: 'new')]
    private string $status;

    #[OA\Property(enum: RealizationTimeSlotEnum::VALUES, example: 'morning')]
    private string $realizationTimeSlot;

    #[OA\Property(example: '2025-01-15')]
    private string $realizationDate;

    #[OA\Property(example: '2025-12-30T10:00:00+00:00')]
    private string $createdAt;

    private OilServiceUserDTO $user;

    private ?RouteDTO $route;

    /**
     * @var OrderStorageContainerMaterialDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: OrderStorageContainerMaterialDTO::class)))]
    private array $materials;

    /**
     * @var OrderInventoryItemDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: OrderInventoryItemDTO::class)))]
    private array $inventoryItems;

    /**
     * @var PriceListItemDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: PriceListItemDTO::class)))]
    private array $priceListItems;

    /**
     * @param OrderStorageContainerMaterialDTO[] $materials
     * @param OrderInventoryItemDTO[] $inventoryItems
     * @param PriceListItemDTO[] $priceListItems
     */
    public function __construct(
        string $id,
        string $ident,
        string $fullName,
        string $phone,
        string $email,
        string $carModel,
        string $licensePlate,
        string $address,
        ?string $note,
        bool $isCompany,
        ?string $companyName,
        ?string $companyIdentificationNumber,
        ?string $companyTaxId,
        ?string $companyAddress,
        ?FileDTO $oilChangeVehiclePhoto,
        ?FileDTO $vinPhoto,
        ?FileDTO $oldOilFilterPhoto,
        ?FileDTO $oldOilPhoto,
        ?FileDTO $odometerPhoto,
        array $otherPhotos,
        string $status,
        string $realizationTimeSlot,
        string $realizationDate,
        string $createdAt,
        OilServiceUserDTO $user,
        ?RouteDTO $route = null,
        array $materials = [],
        array $inventoryItems = [],
        array $priceListItems = [],
    ) {
        $this->id = $id;
        $this->ident = $ident;
        $this->fullName = $fullName;
        $this->phone = $phone;
        $this->email = $email;
        $this->carModel = $carModel;
        $this->licensePlate = $licensePlate;
        $this->address = $address;
        $this->note = $note;
        $this->isCompany = $isCompany;
        $this->companyName = $companyName;
        $this->companyIdentificationNumber = $companyIdentificationNumber;
        $this->companyTaxId = $companyTaxId;
        $this->companyAddress = $companyAddress;
        $this->oilChangeVehiclePhoto = $oilChangeVehiclePhoto;
        $this->vinPhoto = $vinPhoto;
        $this->oldOilFilterPhoto = $oldOilFilterPhoto;
        $this->oldOilPhoto = $oldOilPhoto;
        $this->odometerPhoto = $odometerPhoto;
        $this->otherPhotos = $otherPhotos;
        $this->status = $status;
        $this->realizationTimeSlot = $realizationTimeSlot;
        $this->realizationDate = $realizationDate;
        $this->createdAt = $createdAt;
        $this->user = $user;
        $this->route = $route;
        $this->materials = $materials;
        $this->inventoryItems = $inventoryItems;
        $this->priceListItems = $priceListItems;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getIdent(): string
    {
        return $this->ident;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCarModel(): string
    {
        return $this->carModel;
    }

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function getIsCompany(): bool
    {
        return $this->isCompany;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getCompanyIdentificationNumber(): ?string
    {
        return $this->companyIdentificationNumber;
    }

    public function getCompanyTaxId(): ?string
    {
        return $this->companyTaxId;
    }

    public function getCompanyAddress(): ?string
    {
        return $this->companyAddress;
    }

    public function getOilChangeVehiclePhoto(): ?FileDTO
    {
        return $this->oilChangeVehiclePhoto;
    }

    public function getVinPhoto(): ?FileDTO
    {
        return $this->vinPhoto;
    }

    public function getOldOilFilterPhoto(): ?FileDTO
    {
        return $this->oldOilFilterPhoto;
    }

    public function getOldOilPhoto(): ?FileDTO
    {
        return $this->oldOilPhoto;
    }

    public function getOdometerPhoto(): ?FileDTO
    {
        return $this->odometerPhoto;
    }

    /**
     * @return FileDTO[]
     */
    public function getOtherPhotos(): array
    {
        return $this->otherPhotos;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getRealizationTimeSlot(): string
    {
        return $this->realizationTimeSlot;
    }

    public function getRealizationDate(): string
    {
        return $this->realizationDate;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUser(): OilServiceUserDTO
    {
        return $this->user;
    }

    public function getRoute(): ?RouteDTO
    {
        return $this->route;
    }

    /**
     * @return OrderStorageContainerMaterialDTO[]
     */
    public function getMaterials(): array
    {
        return $this->materials;
    }

    /**
     * @return OrderInventoryItemDTO[]
     */
    public function getInventoryItems(): array
    {
        return $this->inventoryItems;
    }

    /**
     * @return PriceListItemDTO[]
     */
    public function getPriceListItems(): array
    {
        return $this->priceListItems;
    }
}
