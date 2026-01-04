<?php

declare(strict_types=1);

namespace App\Modules\Warehouse\DTO;

use App\Domain\DTOValueResolver;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class WasteMaterialListResponseDTO
{
    #[OA\Property(example: DTOValueResolver::RESULT_SUCCESS)]
    private string $result;

    private int $timestamp;

    /**
     * @var WasteMaterialDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: WasteMaterialDTO::class)))]
    private array $wasteMaterials;

    private int $pageCount;

    /**
     * @param WasteMaterialDTO[] $wasteMaterials
     */
    public function __construct(string $result, int $timestamp, array $wasteMaterials, int $pageCount)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->wasteMaterials = $wasteMaterials;
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
     * @return WasteMaterialDTO[]
     */
    public function getWasteMaterials(): array
    {
        return $this->wasteMaterials;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
