<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class OilServiceUserListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    private int $pageCount;

    /** @var OilServiceUserListDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: OilServiceUserListDTO::class)))]
    private array $users;

    /**
     * @param OilServiceUserListDTO[] $users
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

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return OilServiceUserListDTO[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
