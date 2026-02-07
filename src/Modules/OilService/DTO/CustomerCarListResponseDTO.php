<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class CustomerCarListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    private int $pageCount;

    /** @var CustomerCarDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: CustomerCarDTO::class)))]
    private array $cars;

    /**
     * @param CustomerCarDTO[] $cars
     */
    public function __construct(
        string $result,
        int $timestamp,
        array $cars,
        int $pageCount,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->cars = $cars;
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
     * @return CustomerCarDTO[]
     */
    public function getCars(): array
    {
        return $this->cars;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
