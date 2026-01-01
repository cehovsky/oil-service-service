<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class CarListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var CarDTO[]
     */
    #[OA\Property(
        type: 'array',
        items: new OA\Items(
            ref: new Model(
                type: CarDTO::class
            )
        )
    )]
    private array $cars;

    private int $pageCount;

    /**
     * @param CarDTO[] $cars
     */
    public function __construct(
        string $result,
        int $timestamp,
        array $cars,
        int $pageCount
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
     * @return CarDTO[]
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
