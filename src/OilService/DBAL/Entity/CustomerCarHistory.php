<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Repository\CustomerCarHistoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_customer_car_history')]
#[ORM\Index(name: 'idx_car', columns: ['car_id'])]
#[ORM\Index(name: 'idx_user', columns: ['user_id'])]
#[ORM\Entity(repositoryClass: CustomerCarHistoryRepository::class)]
class CustomerCarHistory
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: CustomerCar::class, inversedBy: 'history')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private CustomerCar $car;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column]
    private DateTimeImmutable $assignedAt;

    public function __construct(
        Uuid $id,
        CustomerCar $car,
        User $user,
        DateTimeImmutable $assignedAt,
    ) {
        $this->id = $id;
        $this->car = $car;
        $this->user = $user;
        $this->assignedAt = $assignedAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCar(): CustomerCar
    {
        return $this->car;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getAssignedAt(): DateTimeImmutable
    {
        return $this->assignedAt;
    }
}
