<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Enum\FormRealizationTimeSlotEnum;
use App\OilService\DBAL\Enum\FormStatusEnum;
use App\OilService\DBAL\Repository\FormRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_form')]
#[ORM\Entity(repositoryClass: FormRepository::class)]
class Form
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

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: FormStatusEnum::class)]
    private FormStatusEnum $status;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: FormRealizationTimeSlotEnum::class)]
    private FormRealizationTimeSlotEnum $realizationTimeSlot;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $realizationDate;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'forms')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

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
        FormStatusEnum $status,
        FormRealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        User $user,
        DateTimeImmutable $createdAt,
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
        $this->status = $status;
        $this->realizationTimeSlot = $realizationTimeSlot;
        $this->realizationDate = $realizationDate;
        $this->user = $user;
        $this->createdAt = $createdAt;
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

    public function getStatus(): FormStatusEnum
    {
        return $this->status;
    }

    public function setStatus(FormStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRealizationTimeSlot(): FormRealizationTimeSlotEnum
    {
        return $this->realizationTimeSlot;
    }

    public function setRealizationTimeSlot(FormRealizationTimeSlotEnum $realizationTimeSlot): self
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
}
