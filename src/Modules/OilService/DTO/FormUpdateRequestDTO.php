<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\OilService\DBAL\Enum\FormRealizationTimeSlotEnum;
use App\OilService\DBAL\Enum\FormStatusEnum;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class FormUpdateRequestDTO
{
    #[OA\Property(example: 'Jan Novák')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $fullName;

    #[OA\Property(example: '+420 123 456 789')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $phone;

    #[OA\Property(example: 'jan.novak@example.com')]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private string $email;

    #[OA\Property(example: 'Škoda Octavia 2.0 TDI')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $carModel;

    #[OA\Property(example: '1A2 3456')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private string $licensePlate;

    #[OA\Property(example: 'Václavské náměstí 1, Praha 1, 110 00')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    private string $address;

    #[OA\Property(example: 'Preferuji odpolední termín', nullable: true)]
    #[Assert\Length(max: 1000)]
    private ?string $note = null;

    #[OA\Property(enum: FormStatusEnum::VALUES, example: 'new')]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [FormStatusEnum::class, 'values'])]
    private string $status;

    #[OA\Property(enum: FormRealizationTimeSlotEnum::VALUES, example: 'morning')]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [FormRealizationTimeSlotEnum::class, 'values'])]
    private string $realizationTimeSlot;

    #[OA\Property(example: '2025-01-15', description: 'Realization date in format YYYY-MM-DD')]
    #[Assert\NotBlank]
    #[Assert\Date]
    private string $realizationDate;

    #[OA\Property(example: false)]
    #[Assert\NotNull]
    private bool $isCompany;

    #[OA\Property(example: 'Novák s.r.o.', nullable: true)]
    #[Assert\Length(max: 255)]
    private ?string $companyName = null;

    #[OA\Property(example: '12345678', nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $companyIdentificationNumber = null;

    #[OA\Property(example: 'CZ12345678', nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $companyTaxId = null;

    #[OA\Property(example: 'Firemní 123, Praha 5, 150 00', nullable: true)]
    #[Assert\Length(max: 500)]
    private ?string $companyAddress = null;

    #[OA\Property(example: 'jan.novak@example.com', description: 'User email - if changed, a new user will be created or existing one linked')]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private string $userEmail;

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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getRealizationTimeSlot(): string
    {
        return $this->realizationTimeSlot;
    }

    public function setRealizationTimeSlot(string $realizationTimeSlot): self
    {
        $this->realizationTimeSlot = $realizationTimeSlot;

        return $this;
    }

    public function getRealizationDate(): string
    {
        return $this->realizationDate;
    }

    public function setRealizationDate(string $realizationDate): self
    {
        $this->realizationDate = $realizationDate;

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

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): self
    {
        $this->userEmail = $userEmail;

        return $this;
    }
}
