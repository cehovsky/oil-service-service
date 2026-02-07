<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Modules\OilService\Validation\Constraint\ExistingCustomerCarIds;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class OilServiceUserCreateRequestDTO
{
    #[OA\Property(example: 'jan.novak@example.com')]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private string $email;

    #[OA\Property(example: '+420 123 456 789')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $phone;

    #[OA\Property(example: 'Jan Novák')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $fullName;

    /**
     * @var string[]|null
     */
    #[OA\Property(type: 'array', items: new OA\Items(type: 'string', example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7'), nullable: true)]
    #[Assert\All([
        new Assert\Uuid(),
    ])]
    #[ExistingCustomerCarIds]
    private ?array $customerCarIds = null;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getCustomerCarIds(): ?array
    {
        return $this->customerCarIds;
    }

    /**
     * @param string[]|null $customerCarIds
     */
    public function setCustomerCarIds(?array $customerCarIds): self
    {
        $this->customerCarIds = $customerCarIds;

        return $this;
    }
}
