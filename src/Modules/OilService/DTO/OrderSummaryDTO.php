<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\OilService\DBAL\Enum\OrderStatusEnum;
use OpenApi\Attributes as OA;

class OrderSummaryDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'O2500001')]
    private string $ident;

    #[OA\Property(example: 'Jan Novák')]
    private string $fullName;

    #[OA\Property(enum: OrderStatusEnum::VALUES, example: 'new')]
    private string $status;

    #[OA\Property(example: '2025-01-15')]
    private string $realizationDate;

    public function __construct(
        string $id,
        string $ident,
        string $fullName,
        string $status,
        string $realizationDate,
    ) {
        $this->id = $id;
        $this->ident = $ident;
        $this->fullName = $fullName;
        $this->status = $status;
        $this->realizationDate = $realizationDate;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getRealizationDate(): string
    {
        return $this->realizationDate;
    }
}
