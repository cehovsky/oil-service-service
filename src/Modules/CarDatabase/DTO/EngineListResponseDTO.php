<?php

declare(strict_types=1);

namespace App\Modules\CarDatabase\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class EngineListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    private int $pageCount;

    /** @var EngineDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: EngineDTO::class)))]
    private array $engines;

    /**
     * @param EngineDTO[] $engines
     */
    public function __construct(string $result, int $timestamp, array $engines, int $pageCount)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->engines = $engines;
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
     * @return EngineDTO[]
     */
    public function getEngines(): array
    {
        return $this->engines;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
