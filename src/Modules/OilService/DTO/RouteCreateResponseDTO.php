<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class RouteCreateResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735559999)]
    private int $timestamp;

    #[OA\Property(ref: new Model(type: RouteDTO::class))]
    private RouteDTO $route;

    public function __construct(
        string $result,
        int $timestamp,
        RouteDTO $route,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->route = $route;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function getRoute(): RouteDTO
    {
        return $this->route;
    }
}
