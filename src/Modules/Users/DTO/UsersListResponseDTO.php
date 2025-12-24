<?php

declare(strict_types=1);

namespace App\Modules\Users\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class UsersListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    private int $pageCount;

    /** @var UserDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: UserDTO::class)))]
    private array $users;

    /**
     * @param UserDTO[] $users
     */
    public function __construct(
        string $result,
        int $timestamp,
        array $users,
        int $pageCount
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->users = $users;
        $this->pageCount = $pageCount;
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

    /**
     * @return UserDTO[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param UserDTO[] $users
     */
    public function setUsers(array $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }

    public function setPageCount(int $pageCount): self
    {
        $this->pageCount = $pageCount;

        return $this;
    }
}
