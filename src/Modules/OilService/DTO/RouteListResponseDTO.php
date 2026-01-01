<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class RouteListResponseDTO
{
    #[OA\Property(example: 'success')]
    private string $result;

    #[OA\Property(example: 1735559999)]
    private int $timestamp;

    /**
     * @var RouteDTO[]
     */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: RouteDTO::class)))]
    private array $routes;

    #[OA\Property(example: 5)]
    private int $pageCount;

    /**
     * @param RouteDTO[] $routes
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
     * @return RouteDTO[]
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
