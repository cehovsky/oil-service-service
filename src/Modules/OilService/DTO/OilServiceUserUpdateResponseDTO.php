<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class OilServiceUserUpdateResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    #[OA\Property(ref: new Model(type: OilServiceUserDTO::class))]
    private OilServiceUserDTO $user;

    public function __construct(
        string $result,
        int $timestamp,
        OilServiceUserDTO $user
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

    public function getUser(): OilServiceUserDTO
    {
        return $this->user;
    }
}
