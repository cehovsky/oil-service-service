<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class CustomerCarHistoryDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(ref: new Model(type: OilServiceUserDTO::class))]
    private OilServiceUserDTO $user;

    #[OA\Property(example: '2025-12-30T10:00:00+00:00')]
    private string $assignedAt;

    public function __construct(
        string $id,
        OilServiceUserDTO $user,
        string $assignedAt,
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->assignedAt = $assignedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): OilServiceUserDTO
    {
        return $this->user;
    }

    public function getAssignedAt(): string
    {
        return $this->assignedAt;
    }
}
