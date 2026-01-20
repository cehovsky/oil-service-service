<?php

declare(strict_types=1);

namespace App\OilService\DBAL\Entity;

use App\OilService\DBAL\Enum\ChatMessageRoleEnum;
use App\OilService\DBAL\Repository\ChatMessageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'oil_service_chat_message')]
#[ORM\Entity(repositoryClass: ChatMessageRepository::class)]
class ChatMessage
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: ChatSession::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ChatSession $session;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, enumType: ChatMessageRoleEnum::class)]
    private ChatMessageRoleEnum $role;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT)]
    private string $content;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(
        Uuid $id,
        ChatSession $session,
        ChatMessageRoleEnum $role,
        string $content,
        DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->session = $session;
        $this->role = $role;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getSession(): ChatSession
    {
        return $this->session;
    }

    public function getRole(): ChatMessageRoleEnum
    {
        return $this->role;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
