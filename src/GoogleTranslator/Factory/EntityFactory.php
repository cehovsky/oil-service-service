<?php

declare(strict_types=1);

namespace App\GoogleTranslator\Factory;

use App\GoogleTranslator\DBAL\Entity\GoogleTranslateCacheItem;
use App\GoogleTranslator\DBAL\Enum\LanguageCodeEnum;
use Symfony\Component\Uid\Factory\UuidFactory;

final readonly class EntityFactory
{
    public function __construct(
        private UuidFactory $uuidFactory,
    ) {
    }

    public function createGoogleTranslateCacheItem(
        LanguageCodeEnum $sourceLanguageCode,
        LanguageCodeEnum $targetLanguageCode,
        string $sourceText,
        string $targetText,
    ): GoogleTranslateCacheItem {
        return new GoogleTranslateCacheItem(
            $this->uuidFactory->timeBased()->create(),
            $sourceLanguageCode,
            $targetLanguageCode,
            $sourceText,
            $targetText,
        );
    }
}
