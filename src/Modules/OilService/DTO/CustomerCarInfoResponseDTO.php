<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class CustomerCarInfoResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: CustomerCarDetailDTO::class))]
    private CustomerCarDetailDTO $detail;

    public function __construct(
        string $result,
        int $timestamp,
        CustomerCarDetailDTO $detail
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->detail = $detail;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getDetail(): CustomerCarDetailDTO
    {
        return $this->detail;
    }
}
