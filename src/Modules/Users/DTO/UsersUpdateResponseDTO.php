<?php

declare(strict_types=1);

namespace App\Modules\Users\DTO;

use App\Domain\DTOValueResolver;
use OpenApi\Attributes as OA;

class UsersUpdateResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    private UserDTO $user;

    public function __construct(
        string $result,
        int $timestamp,
        UserDTO $user
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->user = $user;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getUser(): UserDTO
    {
        return $this->user;
    }

    public function setUser(UserDTO $user): self
    {
        $this->user = $user;

        return $this;
    }
}
