<?php

declare(strict_types=1);

namespace App\GoogleTranslator\DBAL\Entity;

use App\GoogleTranslator\DBAL\Enum\LanguageCodeEnum;
use App\GoogleTranslator\DBAL\Repository\GoogleTranslateCacheItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'coogle_translate_cache_item')]
#[ORM\Entity(repositoryClass: GoogleTranslateCacheItemRepository::class)]
#[ORM\Index(fields: ['sourceLanguageCode', 'targetLanguageCode', 'sourceText'], name: 'search_index')]
class GoogleTranslateCacheItem
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, enumType: LanguageCodeEnum::class)]
    private LanguageCodeEnum $sourceLanguageCode;

    #[ORM\Column(type: Types::STRING, enumType: LanguageCodeEnum::class)]
    private LanguageCodeEnum $targetLanguageCode;

    #[ORM\Column(length: 255)]
    private string $sourceText;

    #[ORM\Column(length: 255)]
    private string $targetText;

    public function __construct(
        Uuid $id,
        LanguageCodeEnum $sourceLanguageCode,
        LanguageCodeEnum $targetLanguageCode,
        string $sourceText,
        string $targetText,
    ) {
        $this->id = $id;
        $this->sourceLanguageCode = $sourceLanguageCode;
        $this->targetLanguageCode = $targetLanguageCode;
        $this->sourceText = $sourceText;
        $this->targetText = $targetText;
    }


    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getSourceLanguageCode(): LanguageCodeEnum
    {
        return $this->sourceLanguageCode;
    }

    public function setSourceLanguageCode(LanguageCodeEnum $sourceLanguageCode): self
    {
        $this->sourceLanguageCode = $sourceLanguageCode;

        return $this;
    }

    public function getTargetLanguageCode(): LanguageCodeEnum
    {
        return $this->targetLanguageCode;
    }

    public function setTargetLanguageCode(LanguageCodeEnum $targetLanguageCode): self
    {
        $this->targetLanguageCode = $targetLanguageCode;

        return $this;
    }

    public function getSourceText(): string
    {
        return $this->sourceText;
    }

    public function setSourceText(string $sourceText): self
    {
        $this->sourceText = $sourceText;

        return $this;
    }

    public function getTargetText(): string
    {
        return $this->targetText;
    }

    public function setTargetText(string $targetText): self
    {
        $this->targetText = $targetText;

        return $this;
    }
}
