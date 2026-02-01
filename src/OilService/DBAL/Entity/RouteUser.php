<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\Auth\DBAL\Entity\User as AuthUser;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_route_user')]
#[ORM\UniqueConstraint(name: 'oil_service_route_user_route_user_unique', columns: ['route_id', 'user_id'])]
#[ORM\Index(name: 'idx_user_route', columns: ['user_id', 'route_id'])]
#[ORM\Entity]
class RouteUser
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Route::class, inversedBy: 'routeUsers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Route $route;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: AuthUser::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private AuthUser $user;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(Uuid $id, Route $route, AuthUser $user, DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->route = $route;
        $this->user = $user;
        $this->createdAt = $createdAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function setRoute(Route $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function getUser(): AuthUser
    {
        return $this->user;
    }

    public function setUser(AuthUser $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
