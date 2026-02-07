<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

class OilServiceUserWithOrdersDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'jan.novak@example.com')]
    private string $email;

    #[OA\Property(example: '+420 123 456 789')]
    private string $phone;

    #[OA\Property(example: 'Jan Novák')]
    private string $fullName;

    #[OA\Property(example: '2025-12-30T10:00:00+00:00')]
    private string $createdAt;

    /** @var OrderDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: OrderDTO::class)))]
    private array $orders;

    /** @var CustomerCarDTO[] */
    #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: CustomerCarDTO::class)))]
    private array $cars;

    /**
     * @param OrderDTO[] $orders
     */
    public function __construct(
        string $id,
        string $email,
        string $phone,
        string $fullName,
        string $createdAt,
        array $orders,
        array $cars = [],
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->phone = $phone;
        $this->fullName = $fullName;
        $this->createdAt = $createdAt;
        $this->orders = $orders;
        $this->cars = $cars;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @return OrderDTO[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * @return CustomerCarDTO[]
     */
    public function getCars(): array
    {
        return $this->cars;
    }
}
