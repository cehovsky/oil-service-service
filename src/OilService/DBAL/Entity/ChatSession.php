<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Enum\ChatSessionStatusEnum;
use App\OilService\DBAL\Repository\ChatSessionRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_chat_session')]
#[ORM\Entity(repositoryClass: ChatSessionRepository::class)]
class ChatSession
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    #[ORM\Column(length: 10)]
    private string $language;

    #[Assert\NotNull]
    #[ORM\Column(type: 'string', enumType: ChatSessionStatusEnum::class)]
    private ChatSessionStatusEnum $status;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $closedAt = null;

    /** @var Collection<int, ChatMessage> */
    #[ORM\OneToMany(mappedBy: 'session', targetEntity: ChatMessage::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $messages;

    public function __construct(
        Uuid $id,
        string $language,
        ChatSessionStatusEnum $status,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->language = $language;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->messages = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;
        $this->touch();

        return $this;
    }

    public function getStatus(): ChatSessionStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ChatSessionStatusEnum $status): self
    {
        $this->status = $status;
        $this->touch();

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function markCompleted(DateTimeImmutable $closedAt): void
    {
        $this->status = ChatSessionStatusEnum::COMPLETED;
        $this->closedAt = $closedAt;
        $this->touch();
    }

    public function markExpired(DateTimeImmutable $closedAt): void
    {
        $this->status = ChatSessionStatusEnum::EXPIRED;
        $this->closedAt = $closedAt;
        $this->touch();
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(ChatMessage $message): void
    {
        if ($this->messages->contains($message)) {
            return;
        }

        $this->messages->add($message);
    }

    public function isActive(): bool
    {
        return $this->status === ChatSessionStatusEnum::ACTIVE;
    }

    public function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
