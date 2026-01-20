<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Enum\ChatKnowledgeItemTypeEnum;
use App\OilService\DBAL\Repository\ChatKnowledgeItemRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_chat_knowledge_item')]
#[ORM\Entity(repositoryClass: ChatKnowledgeItemRepository::class)]
class ChatKnowledgeItem
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private string $name;

    #[Assert\NotBlank]
    #[Assert\Length(max: 8000)]
    #[ORM\Column(type: Types::TEXT)]
    private string $content;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: ChatKnowledgeItemTypeEnum::class)]
    private ChatKnowledgeItemTypeEnum $type;

    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    #[ORM\Column(length: 10)]
    private string $language;

    #[Assert\NotNull]
    #[ORM\Column]
    private bool $isActive;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid $id,
        string $name,
        string $content,
        ChatKnowledgeItemTypeEnum $type,
        string $language,
        bool $isActive,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
        $this->type = $type;
        $this->language = $language;
        $this->isActive = $isActive;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        $this->touch();

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        $this->touch();

        return $this;
    }

    public function getType(): ChatKnowledgeItemTypeEnum
    {
        return $this->type;
    }

    public function setType(ChatKnowledgeItemTypeEnum $type): self
    {
        $this->type = $type;
        $this->touch();

        return $this;
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

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
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

    private function touch(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
