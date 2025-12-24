<?php

declare(strict_types=1);

namespace App\GoogleTranslator;

use App\GoogleTranslator\DBAL\Entity\GoogleTranslateCacheItem;
use App\GoogleTranslator\DBAL\Enum\LanguageCodeEnum;
use App\GoogleTranslator\DBAL\Repository\GoogleTranslateCacheItemRepository;
use App\GoogleTranslator\Exception\TranslateFailedException;
use App\GoogleTranslator\Factory\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Google\Cloud\Core\Exception\ServiceException;
use Google\Cloud\Translate\V2\TranslateClient;

final class GoogleTranslatorManager
{
    private const string SOURCE = 'source';

    private const string TARGET = 'target';

    private const string FORMAT = 'format';

    private const string TEXT = 'text';

    public function __construct(
        private readonly TranslateClient $translateClient,
        private readonly EntityManagerInterface $entityManager,
        private readonly GoogleTranslateCacheItemRepository $googleTranslateCacheItemRepository,
        private readonly EntityFactory $entityFactory,
    ) {
    }

    /**
     * @throws TranslateFailedException
     */
    public function translate(
        LanguageCodeEnum $sourceLanguageCode,
        LanguageCodeEnum $targetLanguageCode,
        string $sourceText
    ): string {
        $cachedGoogleTranslateCacheItem = $this->googleTranslateCacheItemRepository->findTranslationText(
            $sourceLanguageCode,
            $targetLanguageCode,
            $sourceText
        );

        if ($cachedGoogleTranslateCacheItem instanceof GoogleTranslateCacheItem) {
            return $cachedGoogleTranslateCacheItem->getTargetText();
        }

        $googleTranslateCacheItem = $this->entityFactory->createGoogleTranslateCacheItem(
            $sourceLanguageCode,
            $targetLanguageCode,
            $sourceText,
            $this->translateWithoutCache(
                $sourceLanguageCode,
                $targetLanguageCode,
                $sourceText
            ),
        );

        $this->entityManager->persist($googleTranslateCacheItem);
        $this->entityManager->flush();

        return $googleTranslateCacheItem->getTargetText();
    }

    /**
     * @throws TranslateFailedException
     */
    public function translateWithoutCache(
        LanguageCodeEnum $sourceLanguageCode,
        LanguageCodeEnum $targetLanguageCode,
        string $sourceText
    ): string {
        try {
            $response = $this->translateClient->translate(
                $sourceText,
                [
                    self::SOURCE => $sourceLanguageCode,
                    self::TARGET => $targetLanguageCode,
                    self::FORMAT => self::TEXT,
                ]
            );
        } catch (ServiceException $e) {
            throw new TranslateFailedException($e);
        }

        return (string)(is_array($response) ? $response[self::TEXT] ?? $sourceText : $sourceText);
    }
}
