<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Enum\ChatUserRequestStatusEnum;
use App\OilService\DBAL\Repository\ChatUserRequestRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_chat_user_request')]
#[ORM\Entity(repositoryClass: ChatUserRequestRepository::class)]
class ChatUserRequest
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: ChatSession::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?ChatSession $session;

    #[Assert\NotBlank]
    #[Assert\Length(max: 4000)]
    #[ORM\Column(type: Types::TEXT)]
    private string $content;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: ChatUserRequestStatusEnum::class)]
    private ChatUserRequestStatusEnum $status;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isResolved;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $resolvedAt = null;

    #[Assert\Length(max: 4000)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    public function __construct(
        Uuid $id,
        ?ChatSession $session,
        string $content,
        ChatUserRequestStatusEnum $status,
        bool $isResolved,
        DateTimeImmutable $createdAt,
        ?string $note = null,
    ) {
        $this->id = $id;
        $this->session = $session;
        $this->content = $content;
        $this->status = $status;
        $this->isResolved = $isResolved;
        $this->createdAt = $createdAt;
        $this->note = $note;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getSession(): ?ChatSession
    {
        return $this->session;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatus(): ChatUserRequestStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ChatUserRequestStatusEnum $status): self
    {
        $this->status = $status;
        $this->isResolved = $status === ChatUserRequestStatusEnum::RESOLVED;

        if ($this->isResolved) {
            if ($this->resolvedAt === null) {
                $this->resolvedAt = new DateTimeImmutable();
            }
        } else {
            $this->resolvedAt = null;
        }

        return $this;
    }

    public function getIsResolved(): bool
    {
        return $this->isResolved;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getResolvedAt(): ?DateTimeImmutable
    {
        return $this->resolvedAt;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function setIsResolved(bool $isResolved, ?DateTimeImmutable $resolvedAt = null): self
    {
        $this->isResolved = $isResolved;
        $this->status = $isResolved
            ? ChatUserRequestStatusEnum::RESOLVED
            : ChatUserRequestStatusEnum::OPEN;

        if ($isResolved) {
            $this->resolvedAt = $resolvedAt ?? $this->resolvedAt ?? new DateTimeImmutable();
        } else {
            $this->resolvedAt = null;
        }

        return $this;
    }

    public function markResolved(DateTimeImmutable $resolvedAt): void
    {
        $this->setIsResolved(true, $resolvedAt);
    }
}
