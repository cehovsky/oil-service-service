<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class ServiceAreaPolygonResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    private int $timestamp;

    /**
     * @var ServiceAreaPolygonPointDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: ServiceAreaPolygonPointDTO::class)))]
    private array $polygon;

    /**
     * @param ServiceAreaPolygonPointDTO[] $polygon
     */
    public function __construct(string $result, int $timestamp, array $polygon)
    {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->polygon = $polygon;
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
     * @return ServiceAreaPolygonPointDTO[]
     */
    public function getPolygon(): array
    {
        return $this->polygon;
    }
}
