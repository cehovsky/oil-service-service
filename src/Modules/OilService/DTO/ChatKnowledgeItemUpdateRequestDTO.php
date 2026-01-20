<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use App\OilService\DBAL\Enum\ChatKnowledgeItemTypeEnum;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class ChatKnowledgeItemUpdateRequestDTO
{
    #[OA\Property(example: 'Otevírací doba')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[OA\Property(example: 'Standardně pracujeme od 8:00 do 18:00, víkendy po domluvě.')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 8000)]
    private string $content;

    #[OA\Property(enum: ChatKnowledgeItemTypeEnum::VALUES, example: 'knowledge')]
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [ChatKnowledgeItemTypeEnum::class, 'values'])]
    private string $type;

    #[OA\Property(example: 'cs-CZ')]
    #[Assert\NotBlank]
    #[Assert\Length(max: 10)]
    #[Assert\Regex(pattern: '/^[a-z]{2}(?:-[A-Z]{2})?$/')]
    private string $language;

    #[OA\Property(example: true)]
    #[Assert\NotNull]
    private bool $isActive;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
