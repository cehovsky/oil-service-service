<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\OilService\DBAL\Enum\InventoryMovementTypeEnum;
use App\OilService\DBAL\Repository\InventoryItemHistoryRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_inventory_item_history')]
#[ORM\Entity(repositoryClass: InventoryItemHistoryRepository::class)]
class InventoryItemHistory
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: InventoryItem::class, inversedBy: 'history', fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private InventoryItem $inventoryItem;

    #[ORM\ManyToOne(targetEntity: Order::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Order $order;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: InventoryMovementTypeEnum::class)]
    private InventoryMovementTypeEnum $movementType;

    #[Assert\Positive]
    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isIncrement;

    #[Assert\Regex(pattern: '/^\d+(?:\.\d{1,2})?$/')]
    #[ORM\Column(type: Types::DECIMAL, precision: 16, scale: 2, nullable: true)]
    private ?string $price;

    #[Assert\PositiveOrZero]
    #[Assert\Range(min: 0, max: 100)]
    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $vat;

    #[Assert\Regex(pattern: '/^\d+(?:\.\d{1,2})?$/')]
    #[ORM\Column(type: Types::DECIMAL, precision: 16, scale: 2, nullable: true)]
    private ?string $priceVat;

    #[Assert\Length(max: 2000)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: AuthUser::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private AuthUser $createdBy;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(
        Uuid $id,
        InventoryItem $inventoryItem,
        InventoryMovementTypeEnum $movementType,
        int $quantity,
        bool $isIncrement,
        AuthUser $createdBy,
        DateTimeImmutable $createdAt,
        ?Order $order = null,
        ?string $price = null,
        ?int $vat = null,
        ?string $priceVat = null,
        ?string $note = null,
    ) {
        $this->id = $id;
        $this->inventoryItem = $inventoryItem;
        $this->movementType = $movementType;
        $this->quantity = $quantity;
        $this->isIncrement = $isIncrement;
        $this->createdBy = $createdBy;
        $this->createdAt = $createdAt;
        $this->order = $order;
        $this->price = $price;
        $this->vat = $vat;
        $this->priceVat = $priceVat;
        $this->note = $note;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getInventoryItem(): InventoryItem
    {
        return $this->inventoryItem;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getMovementType(): InventoryMovementTypeEnum
    {
        return $this->movementType;
    }

    public function setMovementType(InventoryMovementTypeEnum $movementType): self
    {
        $this->movementType = $movementType;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getIsIncrement(): bool
    {
        return $this->isIncrement;
    }

    public function setIsIncrement(bool $isIncrement): self
    {
        $this->isIncrement = $isIncrement;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getVat(): ?int
    {
        return $this->vat;
    }

    public function setVat(?int $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function getPriceVat(): ?string
    {
        return $this->priceVat;
    }

    public function setPriceVat(?string $priceVat): self
    {
        $this->priceVat = $priceVat;

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

    public function getCreatedBy(): AuthUser
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
