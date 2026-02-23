<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Entity\Order;
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
#[ORM\Index(name: 'idx_status', columns: ['status'])]
#[ORM\Entity(repositoryClass: ChatSessionRepository::class)]
class ChatSession
{
    private const string IDENT_PREFIX = 'S';

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(type: 'integer', unique: true)]
    private int $ident;

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

    #[ORM\OneToOne(targetEntity: Order::class)]
    #[ORM\JoinColumn(nullable: true, unique: true, onDelete: 'SET NULL')]
    private ?Order $order = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $validatedServiceAddress = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $validatedServiceAddressNormalized = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $validatedServiceAddressRecognized = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $validatedServiceAddressWithinServiceArea = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $validatedServiceAddressLatitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $validatedServiceAddressLongitude = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $validatedServiceAddressAt = null;

    public function __construct(
        Uuid $id,
        int $ident,
        string $language,
        ChatSessionStatusEnum $status,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        ?Order $order = null,
    ) {
        $this->id = $id;
        $this->ident = $ident;
        $this->language = $language;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->messages = new ArrayCollection();
        $this->order = $order;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getIdent(): int
    {
        return $this->ident;
    }

    public function getFormattedIdent(): string
    {
        $year = $this->createdAt->format('y');

        return sprintf('%s%s%05d', self::IDENT_PREFIX, $year, $this->ident);
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

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        if ($this->order === $order) {
            return $this;
        }

        $this->order = $order;
        $this->touch();

        return $this;
    }

    public function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getValidatedServiceAddress(): ?string
    {
        return $this->validatedServiceAddress;
    }

    public function getValidatedServiceAddressNormalized(): ?string
    {
        return $this->validatedServiceAddressNormalized;
    }

    public function getValidatedServiceAddressRecognized(): ?bool
    {
        return $this->validatedServiceAddressRecognized;
    }

    public function getValidatedServiceAddressWithinServiceArea(): ?bool
    {
        return $this->validatedServiceAddressWithinServiceArea;
    }

    public function getValidatedServiceAddressLatitude(): ?float
    {
        return $this->validatedServiceAddressLatitude;
    }

    public function getValidatedServiceAddressLongitude(): ?float
    {
        return $this->validatedServiceAddressLongitude;
    }

    public function getValidatedServiceAddressAt(): ?DateTimeImmutable
    {
        return $this->validatedServiceAddressAt;
    }

    public function setValidatedServiceAddressState(
        string $address,
        string $normalizedAddress,
        bool $recognized,
        ?bool $withinServiceArea,
        ?float $latitude,
        ?float $longitude,
        DateTimeImmutable $validatedAt,
    ): self {
        $this->validatedServiceAddress = $address;
        $this->validatedServiceAddressNormalized = $normalizedAddress;
        $this->validatedServiceAddressRecognized = $recognized;
        $this->validatedServiceAddressWithinServiceArea = $withinServiceArea;
        $this->validatedServiceAddressLatitude = $latitude;
        $this->validatedServiceAddressLongitude = $longitude;
        $this->validatedServiceAddressAt = $validatedAt;
        $this->touch();

        return $this;
    }
}
