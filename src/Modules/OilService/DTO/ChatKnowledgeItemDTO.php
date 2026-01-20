<?php

declare(strict_types=1);

namespace App\Modules\OilService\DTO;

use OpenApi\Attributes as OA;

class ChatKnowledgeItemDTO
{
    #[OA\Property(example: 'b7ed468c-d590-4e19-a06c-deec3b2ff6b7')]
    private string $id;

    #[OA\Property(example: 'Proces výměny oleje')]
    private string $name;

    #[OA\Property(example: 'Mechanik přijede k zákazníkovi, zahřeje motor, vypustí starý olej a nahradí jej novým.')] 
    private string $content;

    #[OA\Property(example: 'knowledge')]
    private string $type;

    #[OA\Property(example: 'cs-CZ')]
    private string $language;

    #[OA\Property(example: true)]
    private bool $isActive;

    #[OA\Property(example: '2026-01-20T10:00:00+00:00')]
    private string $createdAt;

    #[OA\Property(example: '2026-01-20T10:00:00+00:00')]
    private string $updatedAt;

    public function __construct(
        string $id,
        string $name,
        string $content,
        string $type,
        string $language,
        bool $isActive,
        string $createdAt,
        string $updatedAt,
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

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }
}
