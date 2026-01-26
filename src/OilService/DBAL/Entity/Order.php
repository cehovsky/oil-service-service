<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\Files\DBAL\Entity\File;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\OilService\DBAL\Enum\OrderStatusEnum;
use App\OilService\DBAL\Repository\OrderRepository;
use App\Warehouse\DBAL\Entity\StorageContainerMaterial;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_order')]
#[ORM\Entity(repositoryClass: OrderRepository::class)]
class Order
{
    private const string IDENT_PREFIX = 'O';

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(type: 'integer', unique: true)]
    private int $ident;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private string $fullName;

    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50)]
    private string $phone;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    #[ORM\Column(length: 180)]
    private string $email;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private string $carModel;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20)]
    private string $licensePlate;

    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    #[ORM\Column(length: 500)]
    private string $address;

    #[Assert\Length(max: 1000)]
    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $note = null;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isCompany;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyName = null;

    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $companyIdentificationNumber = null;

    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $companyTaxId = null;

    #[Assert\Length(max: 500)]
    #[ORM\Column(length: 500, nullable: true)]
    private ?string $companyAddress = null;

    #[ORM\ManyToOne(targetEntity: File::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?File $oilChangeVehiclePhoto = null;

    #[ORM\ManyToOne(targetEntity: File::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?File $vinPhoto = null;

    #[ORM\ManyToOne(targetEntity: File::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?File $oldOilFilterPhoto = null;

    #[ORM\ManyToOne(targetEntity: File::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?File $oldOilPhoto = null;

    #[ORM\ManyToOne(targetEntity: File::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?File $odometerPhoto = null;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: OrderStatusEnum::class)]
    private OrderStatusEnum $status;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: RealizationTimeSlotEnum::class)]
    private RealizationTimeSlotEnum $realizationTimeSlot;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $realizationDate;

    #[ORM\ManyToOne(targetEntity: Route::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Route $route = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    /** @var Collection<int, StorageContainerMaterial> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: StorageContainerMaterial::class)]
    private Collection $storageContainerMaterials;

    /** @var Collection<int, OrderInventoryItem> */
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderInventoryItem::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $orderInventoryItems;

    /** @var Collection<int, PriceListItem> */
    #[ORM\ManyToMany(targetEntity: PriceListItem::class)]
    #[ORM\JoinTable(name: 'oil_service_order_price_list_item')]
    private Collection $priceListItems;

    /** @var Collection<int, File> */
    #[ORM\ManyToMany(targetEntity: File::class)]
    #[ORM\JoinTable(name: 'oil_service_order_other_photo')]
    private Collection $otherPhotos;

    public function __construct(
        Uuid $id,
        int $ident,
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
        ?File $oilChangeVehiclePhoto,
        ?File $vinPhoto,
        ?File $oldOilFilterPhoto,
        ?File $oldOilPhoto,
        ?File $odometerPhoto,
        array $otherPhotos,
        OrderStatusEnum $status,
        RealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        User $user,
        DateTimeImmutable $createdAt,
        ?Route $route = null,
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
        $this->status = $status;
        $this->realizationTimeSlot = $realizationTimeSlot;
        $this->realizationDate = $realizationDate;
        $this->user = $user;
        $this->createdAt = $createdAt;
        $this->route = $route;
        $this->storageContainerMaterials = new ArrayCollection();
        $this->orderInventoryItems = new ArrayCollection();
        $this->priceListItems = new ArrayCollection();
        $this->otherPhotos = new ArrayCollection();

        foreach ($otherPhotos as $otherPhoto) {
            if ($otherPhoto instanceof File && !$this->otherPhotos->contains($otherPhoto)) {
                $this->otherPhotos->add($otherPhoto);
            }
        }
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getIdent(): int
    {
        return $this->ident;
    }

    /**
     * Returns formatted ident in format OYYXXXXX (e.g., O2500001).
     */
    public function getFormattedIdent(): string
    {
        $year = $this->createdAt->format('y');

        return sprintf('%s%s%05d', self::IDENT_PREFIX, $year, $this->ident);
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCarModel(): string
    {
        return $this->carModel;
    }

    public function setCarModel(string $carModel): self
    {
        $this->carModel = $carModel;

        return $this;
    }

    public function getLicensePlate(): string
    {
        return $this->licensePlate;
    }

    public function setLicensePlate(string $licensePlate): self
    {
        $this->licensePlate = $licensePlate;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getIsCompany(): bool
    {
        return $this->isCompany;
    }

    public function setIsCompany(bool $isCompany): self
    {
        $this->isCompany = $isCompany;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getCompanyIdentificationNumber(): ?string
    {
        return $this->companyIdentificationNumber;
    }

    public function setCompanyIdentificationNumber(?string $companyIdentificationNumber): self
    {
        $this->companyIdentificationNumber = $companyIdentificationNumber;

        return $this;
    }

    public function getCompanyTaxId(): ?string
    {
        return $this->companyTaxId;
    }

    public function setCompanyTaxId(?string $companyTaxId): self
    {
        $this->companyTaxId = $companyTaxId;

        return $this;
    }

    public function getCompanyAddress(): ?string
    {
        return $this->companyAddress;
    }

    public function setCompanyAddress(?string $companyAddress): self
    {
        $this->companyAddress = $companyAddress;

        return $this;
    }

    public function getOilChangeVehiclePhoto(): ?File
    {
        return $this->oilChangeVehiclePhoto;
    }

    public function setOilChangeVehiclePhoto(?File $oilChangeVehiclePhoto): self
    {
        $this->oilChangeVehiclePhoto = $oilChangeVehiclePhoto;

        return $this;
    }

    public function getVinPhoto(): ?File
    {
        return $this->vinPhoto;
    }

    public function setVinPhoto(?File $vinPhoto): self
    {
        $this->vinPhoto = $vinPhoto;

        return $this;
    }

    public function getOldOilFilterPhoto(): ?File
    {
        return $this->oldOilFilterPhoto;
    }

    public function setOldOilFilterPhoto(?File $oldOilFilterPhoto): self
    {
        $this->oldOilFilterPhoto = $oldOilFilterPhoto;

        return $this;
    }

    public function getOldOilPhoto(): ?File
    {
        return $this->oldOilPhoto;
    }

    public function setOldOilPhoto(?File $oldOilPhoto): self
    {
        $this->oldOilPhoto = $oldOilPhoto;

        return $this;
    }

    public function getOdometerPhoto(): ?File
    {
        return $this->odometerPhoto;
    }

    public function setOdometerPhoto(?File $odometerPhoto): self
    {
        $this->odometerPhoto = $odometerPhoto;

        return $this;
    }

    public function getStatus(): OrderStatusEnum
    {
        return $this->status;
    }

    public function setStatus(OrderStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRealizationTimeSlot(): RealizationTimeSlotEnum
    {
        return $this->realizationTimeSlot;
    }

    public function setRealizationTimeSlot(RealizationTimeSlotEnum $realizationTimeSlot): self
    {
        $this->realizationTimeSlot = $realizationTimeSlot;

        return $this;
    }

    public function getRealizationDate(): DateTimeImmutable
    {
        return $this->realizationDate;
    }

    public function setRealizationDate(DateTimeImmutable $realizationDate): self
    {
        $this->realizationDate = $realizationDate;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getRoute(): ?Route
    {
        return $this->route;
    }

    public function setRoute(?Route $route): self
    {
        if ($this->route !== null && $this->route !== $route) {
            $this->route->getOrders()->removeElement($this);
        }

        $this->route = $route;

        if ($route !== null && !$route->getOrders()->contains($this)) {
            $route->getOrders()->add($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, StorageContainerMaterial>
     */
    public function getStorageContainerMaterials(): Collection
    {
        return $this->storageContainerMaterials;
    }

    public function addStorageContainerMaterial(StorageContainerMaterial $storageContainerMaterial): self
    {
        if (!$this->storageContainerMaterials->contains($storageContainerMaterial)) {
            $this->storageContainerMaterials->add($storageContainerMaterial);
            $storageContainerMaterial->setOrder($this);
        }

        return $this;
    }

    public function removeStorageContainerMaterial(StorageContainerMaterial $storageContainerMaterial): self
    {
        if ($this->storageContainerMaterials->removeElement($storageContainerMaterial)) {
            if ($storageContainerMaterial->getOrder() === $this) {
                $storageContainerMaterial->setOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderInventoryItem>
     */
    public function getOrderInventoryItems(): Collection
    {
        return $this->orderInventoryItems;
    }

    public function addOrderInventoryItem(OrderInventoryItem $orderInventoryItem): self
    {
        if (!$this->orderInventoryItems->contains($orderInventoryItem)) {
            $this->orderInventoryItems->add($orderInventoryItem);
            $orderInventoryItem->setOrder($this);
        }

        return $this;
    }

    public function removeOrderInventoryItem(OrderInventoryItem $orderInventoryItem): self
    {
        $this->orderInventoryItems->removeElement($orderInventoryItem);

        return $this;
    }

    /**
     * @return Collection<int, PriceListItem>
     */
    public function getPriceListItems(): Collection
    {
        return $this->priceListItems;
    }

    public function addPriceListItem(PriceListItem $priceListItem): self
    {
        if (!$this->priceListItems->contains($priceListItem)) {
            $this->priceListItems->add($priceListItem);
        }

        return $this;
    }

    public function removePriceListItem(PriceListItem $priceListItem): self
    {
        $this->priceListItems->removeElement($priceListItem);

        return $this;
    }

    /**
     * @return Collection<int, File>
     */
    public function getOtherPhotos(): Collection
    {
        return $this->otherPhotos;
    }

    public function addOtherPhoto(File $otherPhoto): self
    {
        if (!$this->otherPhotos->contains($otherPhoto)) {
            $this->otherPhotos->add($otherPhoto);
        }

        return $this;
    }

    public function removeOtherPhoto(File $otherPhoto): self
    {
        $this->otherPhotos->removeElement($otherPhoto);

        return $this;
    }
}
