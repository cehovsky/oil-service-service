<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Repository\PriceListItemRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_price_list_item')]
#[ORM\Entity(repositoryClass: PriceListItemRepository::class)]
class PriceListItem
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

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $invoiceLabel = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DECIMAL, precision: 16, scale: 2)]
    private string $price;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 21])]
    private int $vat;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::DECIMAL, precision: 16, scale: 2)]
    private string $priceVat;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isActive;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isPublic;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isDefault;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isHiddenOnInvoice;

    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[ORM\Column(length: 20, unique: true)]
    private string $code;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $brand = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $externalCode = null;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(
        Uuid $id,
        string $label,
        ?string $description,
        ?string $invoiceLabel,
        string $price,
        int $vat,
        string $priceVat,
        bool $isActive,
        bool $isPublic,
        bool $isDefault,
        bool $isHiddenOnInvoice,
        string $code,
        ?string $brand,
        ?string $externalCode,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->description = $description;
        $this->invoiceLabel = $invoiceLabel;
        $this->price = $price;
        $this->vat = $vat;
        $this->priceVat = $priceVat;
        $this->isActive = $isActive;
        $this->isPublic = $isPublic;
        $this->isDefault = $isDefault;
        $this->isHiddenOnInvoice = $isHiddenOnInvoice;
        $this->code = $code;
        $this->brand = $brand;
        $this->externalCode = $externalCode;
        $this->createdAt = $createdAt;
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

    public function getInvoiceLabel(): ?string
    {
        return $this->invoiceLabel;
    }

    public function setInvoiceLabel(?string $invoiceLabel): self
    {
        $this->invoiceLabel = $invoiceLabel;

        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getVat(): int
    {
        return $this->vat;
    }

    public function setVat(int $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    public function getPriceVat(): string
    {
        return $this->priceVat;
    }

    public function setPriceVat(string $priceVat): self
    {
        $this->priceVat = $priceVat;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIsPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getIsDefault(): bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function getIsHiddenOnInvoice(): bool
    {
        return $this->isHiddenOnInvoice;
    }

    public function setIsHiddenOnInvoice(bool $isHiddenOnInvoice): self
    {
        $this->isHiddenOnInvoice = $isHiddenOnInvoice;

        return $this;
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

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
