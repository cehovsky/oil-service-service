<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class OilServiceUserInfoResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: OilServiceUserWithOrdersDTO::class))]
    private OilServiceUserWithOrdersDTO $user;

    public function __construct(
        string $result,
        int $timestamp,
        OilServiceUserWithOrdersDTO $user
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->user = $user;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getUser(): OilServiceUserWithOrdersDTO
    {
        return $this->user;
    }
}
