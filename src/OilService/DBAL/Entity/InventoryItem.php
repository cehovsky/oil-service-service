<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\Auth\DBAL\Entity\User as AuthUser;
use App\OilService\DBAL\Repository\InventoryItemRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_inventory_item')]
#[ORM\Entity(repositoryClass: InventoryItemRepository::class)]
class InventoryItem
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private string $label;

    #[Assert\Length(max: 2000)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, unique: true)]
    private string $code;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalCode = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 16, scale: 2, nullable: true)]
    private ?string $price;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $vat;

    #[ORM\Column(type: Types::DECIMAL, precision: 16, scale: 2, nullable: true)]
    private ?string $priceVat;

    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER)]
    private int $stockCount;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: AuthUser::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private AuthUser $createdBy;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: AuthUser::class, fetch: 'LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    private AuthUser $updatedBy;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, InventoryItemHistory>&Selectable<int, InventoryItemHistory> */
    #[ORM\OneToMany(mappedBy: 'inventoryItem', targetEntity: InventoryItemHistory::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $history;

    /** @var Collection<int, OrderInventoryItem> */
    #[ORM\OneToMany(mappedBy: 'inventoryItem', targetEntity: OrderInventoryItem::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $orderInventoryItems;

    public function __construct(
        Uuid $id,
        string $label,
        ?string $description,
        string $code,
        ?string $externalCode,
        ?string $price,
        ?int $vat,
        ?string $priceVat,
        int $stockCount,
        AuthUser $createdBy,
        AuthUser $updatedBy,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->description = $description;
        $this->code = $code;
        $this->externalCode = $externalCode;
        $this->price = $price;
        $this->vat = $vat;
        $this->priceVat = $priceVat;
        $this->stockCount = $stockCount;
        $this->createdBy = $createdBy;
        $this->updatedBy = $updatedBy;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->history = new ArrayCollection();
        $this->orderInventoryItems = new ArrayCollection();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getExternalCode(): ?string
    {
        return $this->externalCode;
    }

    public function setExternalCode(?string $externalCode): self
    {
        $this->externalCode = $externalCode;

        return $this;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getStockCount(): int
    {
        return $this->stockCount;
    }

    public function setStockCount(int $stockCount): self
    {
        $this->stockCount = $stockCount;

        return $this;
    }

    public function getCreatedBy(): AuthUser
    {
        return $this->createdBy;
    }

    public function setCreatedBy(AuthUser $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): AuthUser
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(AuthUser $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, InventoryItemHistory>&Selectable<int, InventoryItemHistory>
     */
    public function getHistory(): Collection
    {
        return $this->history;
    }

    public function addHistory(InventoryItemHistory $history): self
    {
        if (!$this->history->contains($history)) {
            $this->history->add($history);
        }

        return $this;
    }

    public function removeHistory(InventoryItemHistory $history): self
    {
        $this->history->removeElement($history);

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
        }

        return $this;
    }

    public function removeOrderInventoryItem(OrderInventoryItem $orderInventoryItem): self
    {
        $this->orderInventoryItems->removeElement($orderInventoryItem);

        return $this;
    }
}
