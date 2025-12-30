<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class OilServiceUserListDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'jan.novak@example.com')]
    private string $email;

    #[OA\Property(example: '+420 123 456 789')]
    private string $phone;

    #[OA\Property(example: 'Jan Novák')]
    private string $fullName;

    #[OA\Property(example: '2025-12-30T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: 5)]
    private int $formsCount;

    public function __construct(
        string $id,
        string $email,
        string $phone,
        string $fullName,
        string $createdAt,
        int $formsCount,
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->phone = $phone;
        $this->fullName = $fullName;
        $this->createdAt = $createdAt;
        $this->formsCount = $formsCount;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getFormsCount(): int
    {
        return $this->formsCount;
    }
}
