<?php

declare(strict_types=1);

namespace App\Modules\CarApp\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class CarAppRouteListResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735559999)]
    private int $timestamp;

    /**
     * @var CarAppRouteDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: CarAppRouteDTO::class)))]
    private array $routes;

    #[OA\Property(example: 1)]
    private int $pageCount;

    /**
     * @param CarAppRouteDTO[] $routes
     */
    public function __construct(
        string $result,
        int $timestamp,
        array $routes,
        int $pageCount,
    ) {
        $this->result = $result;
        $this->timestamp = $timestamp;
        $this->routes = $routes;
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
     * @return CarAppRouteDTO[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getPageCount(): int
    {
        return $this->pageCount;
    }
}
